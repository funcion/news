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

        // Build the article fresh — use new + setTranslation so we never
        // pass a PHP array into a column that may still be VARCHAR(255).
        $article = new Article();
        $article->raw_article_id = $this->rawArticle->id;
        $article->slug_en        = $slugEn;
        $article->slug_es        = $slugEs;
        $article->author_id      = $author->id;
        $article->category_id    = $categoryId;
        $article->status         = 'draft';
        $article->meta_keywords  = $redacted['keywords'] ?? [];
        $article->reading_time   = $this->calculateReadingTime($contentEn);
        $article->ai_metadata    = [
            'origin_url' => $this->rawArticle->url,
            'today_date' => $today,
            'json_ld'    => $redacted['json_ld'] ?? null,
        ];

        // Set all translatable fields via setTranslation (Spatie-aware)
        $article->setTranslation('title',            'en', $redacted['title_en']  ?? $this->rawArticle->title);
        $article->setTranslation('title',            'es', $redacted['title_es']  ?? $this->rawArticle->title);
        $article->setTranslation('excerpt',          'en', $redacted['excerpt_en'] ?? '');
        $article->setTranslation('excerpt',          'es', $redacted['excerpt_es'] ?? '');
        $article->setTranslation('meta_title',       'en', Str::limit($redacted['meta_title_en'] ?? $redacted['title_en'] ?? '', 70));
        $article->setTranslation('meta_title',       'es', Str::limit($redacted['meta_title_es'] ?? $redacted['title_es'] ?? '', 70));
        $article->setTranslation('meta_description', 'en', Str::limit($redacted['excerpt_en'] ?? '', 160));
        $article->setTranslation('meta_description', 'es', Str::limit($redacted['excerpt_es'] ?? '', 160));
        // content will be set after image injection
        $article->setTranslation('content', 'en', '');
        $article->setTranslation('content', 'es', '');
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

        $wordTargets = [
            'news'   => '600-900 words EN | 600-900 palabras ES',
            'blog'   => '1200-2000 words EN | 1200-2000 palabras ES',
            'guide'  => '1500-2500 words EN | 1500-2500 palabras ES',
            'review' => '1500-3000 words EN | 1500-3000 palabras ES',
            'pillar' => '2500-5000 words EN | 2500-5000 palabras ES',
        ];
        $wordTarget = $wordTargets[$contentType] ?? $wordTargets['blog'];

        $prompt = <<<PROMPT
You are a world-class bilingual Senior Journalist and SEO Strategist (15+ years experience).
DATE: {$today} | TYPE: {$contentType} | TARGET LENGTH: {$wordTarget}
TOPIC: {$topic}

=== MANDATORY QUALITY STANDARDS ===

SEO - Google E-E-A-T (10/10 REQUIRED):
- Primary keyword in title (first 60 chars), first paragraph, at least 2 H2s, meta description.
- Use semantic LSI keywords naturally throughout.
- Title EN and ES: max 60 chars each.
- Excerpt/meta description EN and ES: max 155 chars each.
- Slug: lowercase-hyphens-no-special-chars (max 6 words each).

CONTENT QUALITY - Google Helpful Content:
- NO cliches: forbidden = "paradigm shift", "game-changer", "revolutionary", "cambio de paradigma".
- BURSTINESS: alternate short punchy sentences (3-5 words) with complex analytical ones.
- Include 1 real or fictional micro-story per section.
- Use: H2 headings, <strong> for key facts, <blockquote> for quotes, <ul> or <ol> for lists.

ADA/WCAG 2.1 AAA ACCESSIBILITY:
- For images: ONLY place bare placeholder [IMAGE_1] on its own line. NO extra HTML around it.
- All image alt texts (in image_prompts) must describe the image vividly for a blind reader.
- Use semantic HTML throughout: h2, h3, p, ul, ol, blockquote, strong.

IMAGES - FLUX.1 Ultra-Realism:
- Generate 2 to 4 photorealistic image prompts.
- If humans appear: add "hyper-realistic skin textures, pores, authentic facial expression, natural lighting, 35mm DSLR Nikon D850, 8k resolution, no text, no watermarks".

=== OUTPUT: STRICT JSON ONLY (no markdown, no text outside JSON) ===
{
    "title_en": "Compelling English title max 60 chars",
    "title_es": "Titulo en Espanol max 60 caracteres",
    "slug_en": "english-seo-slug-max-6-words",
    "slug_es": "slug-en-espanol-max-6-palabras",
    "excerpt_en": "Persuasive English meta description max 155 chars",
    "excerpt_es": "Meta descripcion en Espanol max 155 chars",
    "keywords": ["primary keyword", "secondary kw", "lsi kw 1", "lsi kw 2"],
    "content_en": "<p>Full English HTML article body. Place [IMAGE_1] on its own line between paragraphs.</p>",
    "content_es": "<p>Cuerpo del articulo en Espanol. Coloca [IMAGE_1] en su propia linea entre parrafos.</p>",
    "image_prompts": [
        {
            "id": "[IMAGE_1]",
            "prompt_en": "Photojournalistic style, [specific scene], hyper-realistic, 35mm lens, 8k, no text, no watermarks.",
            "alt_en": "Descriptive alt text for screen readers in English",
            "alt_es": "Texto alt descriptivo para lectores de pantalla en Espanol",
            "caption_en": "Informative English image caption",
            "caption_es": "Leyenda informativa en Espanol",
            "title_en": "English SEO image title max 70 chars",
            "title_es": "Titulo SEO imagen en Espanol max 70 chars"
        }
    ],
    "json_ld": {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": "English title here",
        "datePublished": "{$today}",
        "author": {"@type": "Person", "name": "{$author->name}"},
        "description": "English meta description"
    }
}
PROMPT;

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
