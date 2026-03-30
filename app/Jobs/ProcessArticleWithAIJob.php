<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\Author;
use App\Models\RawArticle;
use App\Services\AI\OpenRouterService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessArticleWithAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // Slightly higher for bilingual generation
    public $tries = 3;

    public function backoff(): array
    {
        return [10, 30, 60];
    }

    public function __construct(
        protected RawArticle $rawArticle
    ) {}

    public function handle(OpenRouterService $ai, \App\Services\AI\SiliconFlowImageService $imageService): void
    {
        $today = now()->format('l, F j, Y');
        Log::info("Processing RawArticle: {$this->rawArticle->id} (Bilingual EN/ES) at {$today}.");

        if ($this->rawArticle->status !== 'pending') {
            Log::warning("RawArticle {$this->rawArticle->id} already processed.");
            return;
        }

        $classification = $this->classifyAndExtract($ai);

        if ($classification === null) {
            throw new \Exception("AI classification failed. Retrying...");
        }

        if (!($classification['is_relevant'] ?? false) && empty($classification['is_seed'])) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::info("RawArticle {$this->rawArticle->id} ignored by AI (not relevant).");
            return;
        }

        $author = Author::ai()->active()->inRandomOrder()->first();

        if (!$author) {
            $author = Author::create([
                'name'        => 'AI Reporter',
                'slug'        => 'ai-reporter',
                'type'        => 'ai',
                'is_active'   => true,
                'voice_style' => 'The Divulger',
                'bio'         => 'AI optimized for tech news writing.',
            ]);
        }

        $redacted = $this->redactBilingual($ai, $classification, $author);

        if (!$redacted) {
            throw new \RuntimeException("AI could not draft bilingual content (attempt {$this->attempts()}).");
        }

        // --- CLEANUP: Remove AI hallucinated image attributes from BOTH lang contents ---
        $contentEn = $this->cleanHallucinatedAttributes($redacted['content_en'] ?? '');
        $contentEs = $this->cleanHallucinatedAttributes($redacted['content_es'] ?? $contentEn);

        // Determine category
        $categoryId = $this->rawArticle->source->category_id ?? 1;
        if (!empty($classification['category_name'])) {
            $matchedCat = \App\Models\Category::whereRaw("name->>'es' ILIKE ?", [trim($classification['category_name'])])->first()
                ?? \App\Models\Category::whereRaw("name->>'en' ILIKE ?", [trim($classification['category_name'])])->first();
            if ($matchedCat) {
                $categoryId = $matchedCat->id;
            }
        }

        // --- CREATE ARTICLE (Bilingual) ---
        $slugEn = $redacted['slug_en'] ?? Str::slug($redacted['title_en'] ?? $this->rawArticle->title);
        $slugEs = $redacted['slug_es'] ?? Str::slug($redacted['title_es'] ?? $this->rawArticle->title);

        // Ensure unique slugs
        $slugEn = $this->ensureUniqueSlug($slugEn, 'slug_en');
        $slugEs = $this->ensureUniqueSlug($slugEs, 'slug_es');

        $article = Article::create([
            'raw_article_id'    => $this->rawArticle->id,
            'slug_en'           => $slugEn,
            'slug_es'           => $slugEs,
            'author_id'         => $author->id,
            'category_id'       => $categoryId,
            'status'            => 'draft',
            'meta_keywords'     => $redacted['keywords'] ?? [],
            'reading_time'      => $this->calculateReadingTime($contentEn),
            'ai_metadata'       => [
                'origin_url' => $this->rawArticle->url,
                'today_date' => $today,
                'json_ld'    => $redacted['json_ld'] ?? null,
            ],
            // Translatable fields (empty first, filled below)
            'title'             => ['en' => '', 'es' => ''],
            'content'           => ['en' => '', 'es' => ''],
            'excerpt'           => ['en' => '', 'es' => ''],
            'meta_title'        => ['en' => '', 'es' => ''],
            'meta_description'  => ['en' => '', 'es' => ''],
        ]);

        // Set translations explicitly
        $article->setTranslation('title', 'en', $redacted['title_en'] ?? $this->rawArticle->title);
        $article->setTranslation('title', 'es', $redacted['title_es'] ?? $this->rawArticle->title);
        $article->setTranslation('excerpt', 'en', $redacted['excerpt_en'] ?? '');
        $article->setTranslation('excerpt', 'es', $redacted['excerpt_es'] ?? '');
        $article->setTranslation('meta_title', 'en', $redacted['meta_title_en'] ?? $redacted['title_en'] ?? '');
        $article->setTranslation('meta_title', 'es', $redacted['meta_title_es'] ?? $redacted['title_es'] ?? '');
        $article->setTranslation('meta_description', 'en', $redacted['excerpt_en'] ?? '');
        $article->setTranslation('meta_description', 'es', $redacted['excerpt_es'] ?? '');
        $article->save();

        // --- IMAGE GENERATION (shared across languages) ---
        $imageCount = 0;
        $imageObjectsJsonLd = [];

        if (!empty($redacted['image_prompts']) && is_array($redacted['image_prompts'])) {
            foreach ($redacted['image_prompts'] as $index => $imgData) {
                if ($index >= 5) break;

                $placeholder = $imgData['id'] ?? '';
                $promptEn    = $imgData['prompt_en'] ?? '';
                $altEn       = $imgData['alt_en'] ?? '';
                $altEs       = $imgData['alt_es'] ?? $altEn;
                $captionEn   = $imgData['caption_en'] ?? $altEn;
                $captionEs   = $imgData['caption_es'] ?? $altEs;
                $titleEn     = $imgData['title_en'] ?? $altEn;
                $titleEs     = $imgData['title_es'] ?? $altEs;

                if (empty($placeholder) || empty($promptEn)) continue;

                $path = $imageService->generateAndSave($promptEn, $slugEn, $index + 1);

                if ($path && file_exists($path)) {
                    $media = $article->addMedia($path)
                        ->withCustomProperties([
                            'alt_en'     => $altEn,
                            'alt_es'     => $altEs,
                            'caption_en' => $captionEn,
                            'caption_es' => $captionEs,
                            'title_en'   => $titleEn,
                            'title_es'   => $titleEs,
                        ])
                        ->toMediaCollection('images');

                    $urlOriginal = $media->getUrl();
                    $urlThumb    = $media->getUrl('thumb');
                    $urlMedium   = $media->getUrl('medium');
                    $urlLarge    = $media->getUrl('large');
                    $srcset      = "{$urlThumb} 480w, {$urlMedium} 800w, {$urlLarge} 1200w";
                    $sizes       = "(max-width: 800px) 100vw, 800px";
                    $imgId       = "img-" . ($index + 1) . "-" . Str::random(5);

                    // Build HTML for each language
                    $imgTagEn = $this->buildImageTag($urlOriginal, $srcset, $sizes, $altEn, $titleEn, $captionEn, $imgId);
                    $imgTagEs = $this->buildImageTag($urlOriginal, $srcset, $sizes, $altEs, $titleEs, $captionEs, $imgId);

                    $contentEn = str_replace($placeholder, $imgTagEn, $contentEn);
                    $contentEs = str_replace($placeholder, $imgTagEs, $contentEs);

                    $imageObjectsJsonLd[] = [
                        "@type"       => "ImageObject",
                        "url"         => $urlOriginal,
                        "caption"     => $captionEn,
                        "description" => $altEn,
                        "width"       => 1280,
                        "height"      => 720,
                    ];

                    if ($imageCount === 0) {
                        $article->update([
                            'image_url' => $urlOriginal,
                        ]);
                        $article->setTranslation('image_alt', 'en', $altEn);
                        $article->setTranslation('image_alt', 'es', $altEs);
                        $article->save();
                    }

                    $imageCount++;
                } else {
                    $contentEn = str_replace($placeholder, '', $contentEn);
                    $contentEs = str_replace($placeholder, '', $contentEs);
                }
            }
        }

        // Save final bilingual content
        $article->setTranslation('content', 'en', $contentEn);
        $article->setTranslation('content', 'es', $contentEs);

        // Update JSON-LD
        if (!empty($imageObjectsJsonLd)) {
            $meta = $article->ai_metadata;
            $meta['json_ld']['image'] = $imageObjectsJsonLd;
            $article->ai_metadata = $meta;
        }

        $article->save();

        $this->rawArticle->update(['status' => 'processed']);
        Log::info("Bilingual article created: {$article->id} with {$imageCount} images.");
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private function cleanHallucinatedAttributes(string $content): string
    {
        $content = preg_replace('/\s*\"\s*alt=\"\[IMAGE_\d+_ALT\]\"\s*title=\"\[IMAGE_\d+_TITLE\]\">\s*/i', '', $content);
        $content = preg_replace('/\[IMAGE_\d+_(ALT|TITLE|CAPTION|PROMPT)\]/i', '', $content);
        $content = preg_replace('/\s*\"\s*alt=\"[^\"]*\"\s*title=\"[^\"]*\">\s*/i', '', $content);
        return $content;
    }

    private function buildImageTag(
        string $src, string $srcset, string $sizes,
        string $alt, string $title, string $caption, string $imgId
    ): string {
        return "<figure role=\"group\" aria-labelledby=\"caption-{$imgId}\" class=\"article-image my-10 overflow-hidden rounded-xl border border-gray-100 shadow-2xl transition-all duration-500 hover:shadow-cyan-500/20\">
            <img src=\"{$src}\"
                 srcset=\"{$srcset}\"
                 sizes=\"{$sizes}\"
                 alt=\"{$alt}\"
                 title=\"{$title}\"
                 loading=\"lazy\"
                 decoding=\"async\"
                 width=\"1280\"
                 height=\"720\"
                 role=\"img\"
                 class=\"w-full h-auto object-cover aspect-video\">
            <figcaption id=\"caption-{$imgId}\" class=\"text-sm text-gray-500 mt-4 text-center italic leading-relaxed px-4 bg-gray-50/50 py-3 border-t border-gray-100\">
                {$caption}
            </figcaption>
        </figure>";
    }

    private function ensureUniqueSlug(string $slug, string $column, int $attempt = 0): string
    {
        $candidate = $attempt === 0 ? $slug : "{$slug}-{$attempt}";
        if (Article::where($column, $candidate)->exists()) {
            return $this->ensureUniqueSlug($slug, $column, $attempt + 1);
        }
        return $candidate;
    }

    protected function classifyAndExtract(OpenRouterService $ai): ?array
    {
        $content = trim(strip_tags($this->rawArticle->content ?? ''));
        $today   = now()->format('l, F j, Y');

        if (empty($content)) {
            Log::info("RawArticle {$this->rawArticle->id} has no content. Treating as a Seed Idea.");
            $categories = \App\Models\Category::active()->get()->map(fn($c) => $c->getTranslation('name', 'es'))->toArray();
            return [
                'is_relevant'   => true,
                'importance'    => 8,
                'is_seed'       => true,
                'category_name' => $categories[0] ?? 'General',
                'facts'         => [$this->rawArticle->title],
            ];
        }

        $categories     = \App\Models\Category::active()->get()->map(fn($c) => $c->getTranslation('name', 'es'))->toArray();
        $categoriesList = implode(', ', $categories) ?: 'General';

        $prompt = "You are a senior editorial AI. Classify this news article.
        DATE: {$today}
        VALID CATEGORIES: [{$categoriesList}]
        NEWS: Title: {$this->rawArticle->title} | Content: {$content}
        Respond in strict JSON: {\"is_relevant\": bool, \"category_name\": string, \"content_type\": \"news|blog|guide|review|pillar\", \"importance\": int, \"facts\": [string]}";

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_GEMINI_LATEST);
        return $this->parseJson($response);
    }

    protected function redactBilingual(OpenRouterService $ai, array $classification, Author $author): ?array
    {
        $today       = now()->format('l, F j, Y');
        $isSeed      = $classification['is_seed'] ?? false;
        $contentType = $classification['content_type'] ?? 'blog';
        $topic       = $isSeed ? $this->rawArticle->title : implode('; ', $classification['facts']);

        $prompt = "You are a bilingual Senior Journalist (EN/ES) and SEO Strategist.
        DATE: {$today} | TYPE: {$contentType}

        TOPIC/FACTS: {$topic}

        CRITICAL RULES:
        1. Write the full article in BOTH English AND Spanish simultaneously.
        2. For images: place ONLY the bare placeholder [IMAGE_1] on its own line. NO extra HTML tags, NO alt/title attributes in the text.
        3. Generate 2-4 photorealistic image prompts for FLUX.1.
        4. For humans in images: require 'hyper-realistic skin, pores, natural lighting, 35mm DSLR, 8k'.

        Respond STRICTLY in this JSON format (no markdown wrapping):
        {
            \"title_en\": \"SEO-optimized English title\",
            \"title_es\": \"Título en Español optimizado para SEO\",
            \"slug_en\": \"english-url-slug\",
            \"slug_es\": \"slug-en-espanol\",
            \"excerpt_en\": \"English meta description (max 155 chars)\",
            \"excerpt_es\": \"Meta descripción en Español (máx 155 chars)\",
            \"keywords\": [\"keyword1\", \"keyword2\"],
            \"content_en\": \"<p>Full English HTML content with [IMAGE_1] placeholders...</p>\",
            \"content_es\": \"<p>Contenido HTML completo en Español con placeholders [IMAGE_1]...</p>\",
            \"image_prompts\": [
                {
                    \"id\": \"[IMAGE_1]\",
                    \"prompt_en\": \"Hyper-realistic editorial photo... 8k, 35mm, no text.\",
                    \"alt_en\": \"Descriptive alt text in English\",
                    \"alt_es\": \"Texto alternativo descriptivo en Español\",
                    \"caption_en\": \"English caption\",
                    \"caption_es\": \"Leyenda en Español\",
                    \"title_en\": \"English SEO image title\",
                    \"title_es\": \"Título SEO de imagen en Español\"
                }
            ],
            \"json_ld\": {\"@context\": \"https://schema.org\", \"@type\": \"NewsArticle\", \"headline\": \"title\", \"datePublished\": \"{$today}\"}
        }";

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_GEMINI_LATEST);
        $data     = $this->parseJson($response);

        if (isset($data['keywords']) && is_string($data['keywords'])) {
            $data['keywords'] = array_map('trim', explode(',', $data['keywords']));
        }

        return $data;
    }

    protected function parseJson(?string $json): ?array
    {
        if (!$json) return null;
        $clean = preg_replace('/```json|```/', '', $json);
        $result = json_decode(trim($clean), true);
        return $result ?: null;
    }

    protected function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200));
    }

    public function failed(\Throwable $exception): void
    {
        $this->rawArticle->update(['status' => 'failed']);
        Log::error("Job failed for RawArticle {$this->rawArticle->id}: {$exception->getMessage()}");
    }
}
