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

    public function handle(OpenRouterService $ai, \App\Services\AI\SiliconFlowImageService $imageService, \App\Services\AI\TagGeneratorService $tagService, \App\Services\AI\DuplicateCheckerService $duplicateChecker): void
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

        // --- NEW: DUPLICATE CHECK LEVEL 2 & 3 ---
        $isDuplicate = $duplicateChecker->checkAndHandleDuplicate(
             $this->rawArticle->title, 
             $this->rawArticle->content, 
             $this->rawArticle->url, 
             $this->rawArticle->id
        );

        if ($isDuplicate) {
             $this->rawArticle->update(['status' => 'processed']);
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
        $article->seo_score      = $redacted['seo_score'] ?? 85; 
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
                $altEn       = trim($imgData['alt_en'] ?? '');
                $altEs       = trim($imgData['alt_es'] ?? $altEn);
                $captionEn   = trim($imgData['caption_en'] ?? $altEn);
                $captionEs   = trim($imgData['caption_es'] ?? $altEs);
                $titleEn     = Str::limit(trim($imgData['title_en'] ?? $altEn), 70);
                $titleEs     = Str::limit(trim($imgData['title_es'] ?? $altEs), 70);

                if (empty($placeholder) || empty($promptEn)) continue;

                // ── 1 API call to SiliconFlow ─────────────────────────────
                $path = $imageService->generateAndSave($promptEn, $slugEn, $index + 1);

                if ($path && file_exists($path)) {

                    $imgNum  = $index + 1;
                    $imgId   = "img-{$imgNum}-" . Str::random(5);
                    $sizes   = "(max-width: 600px) 100vw, (max-width: 1200px) 800px, 1200px";

                    // ── Save EN copy (preservingOriginal keeps the file for ES) ──
                    $fileNameEn = "{$slugEn}-{$imgNum}.webp";
                    $mediaEn = $article->addMedia($path)
                        ->usingFileName($fileNameEn)
                        ->usingName(Str::limit($titleEn, 70))
                        ->withCustomProperties([
                            'lang'    => 'en',
                            'alt'     => $altEn,
                            'title'   => $titleEn,
                            'caption' => $captionEn,
                        ])
                        ->preservingOriginal()                  // keeps the source file for ES
                        ->toMediaCollection('images_en');

                    // ── Save ES copy (moves the file, nothing left on disk) ──
                    $fileNameEs = "{$slugEs}-{$imgNum}.webp";
                    $mediaEs = $article->addMedia($path)
                        ->usingFileName($fileNameEs)
                        ->usingName(Str::limit($titleEs, 70))
                        ->withCustomProperties([
                            'lang'    => 'es',
                            'alt'     => $altEs,
                            'title'   => $titleEs,
                            'caption' => $captionEs,
                        ])
                        ->toMediaCollection('images_es');

                    // ── Build srcset for EACH language from its own collection ──
                    $srcsetEn = $mediaEn->getUrl('thumb') . " 480w, "
                              . $mediaEn->getUrl('medium') . " 800w, "
                              . $mediaEn->getUrl('large')  . " 1200w";

                    $srcsetEs = $mediaEs->getUrl('thumb') . " 480w, "
                              . $mediaEs->getUrl('medium') . " 800w, "
                              . $mediaEs->getUrl('large')  . " 1200w";

                    // ── Build semantic, WCAG-compliant <figure> for each language ──
                    $imgTagEn = $this->buildImageTag(
                        $mediaEn->getUrl(), $srcsetEn, $sizes, $altEn, $titleEn, $captionEn, $imgId
                    );
                    $imgTagEs = $this->buildImageTag(
                        $mediaEs->getUrl(), $srcsetEs, $sizes, $altEs, $titleEs, $captionEs, $imgId
                    );

                    $contentEn = str_replace($placeholder, $imgTagEn, $contentEn);
                    $contentEs = str_replace($placeholder, $imgTagEs, $contentEs);

                    // ── JSON-LD Schema.org ImageObject (Google SEO) ──
                    $imageObjectsJsonLd[] = [
                        "@type"       => "ImageObject",
                        "url"         => $mediaEn->getUrl('large'),
                        "thumbnail"   => $mediaEn->getUrl('thumb'),
                        "caption"     => $captionEn,
                        "description" => $altEn,
                        "name"        => $titleEn,
                        "width"       => 1200,
                        "height"      => 675,
                        "encodingFormat" => "image/webp",
                        "inLanguage"  => "en",
                    ];

                    // ── Set featured image on first image only ──
                    if ($imageCount === 0) {
                        $article->image_url = $mediaEn->getUrl('large'); // use large for og:image
                        $article->setTranslation('image_alt', 'en', $altEn);
                        $article->setTranslation('image_alt', 'es', $altEs);
                        $article->save();
                    }

                    Log::info("Image {$imgNum} saved: EN={$fileNameEn}, ES={$fileNameEs}");
                    $imageCount++;

                } else {
                    // Image generation failed — remove placeholder from both languages
                    $contentEn = str_replace($placeholder, '', $contentEn);
                    $contentEs = str_replace($placeholder, '', $contentEs);
                    Log::warning("Image {$index} generation failed for article. Placeholder removed.");
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

        // --- Generate Embedding ---
        $duplicateChecker->generateAndStoreEmbedding($article, $contentEn);

        // --- Generate and sync Tags ---
        $extractedTags = $tagService->generateTags($contentEn);
        if (!empty($extractedTags)) {
            $tagService->syncTagsToArticle($article, $extractedTags);
            Log::info("Tags generated for Article {$article->id}: " . implode(', ', $extractedTags));
        }

        $this->rawArticle->update(['status' => 'processed']);
        
        // --- Publish Realtime Event ---
        if ($article->status === 'published' || $article->status === 'draft') {
            event(new \App\Events\ArticlePublished($article));
        }
        
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
            // Try to get category in both languages
            $categories = \App\Models\Category::active()->get()->map(function ($c) {
                $en = $c->getTranslation('name', 'en');
                $es = $c->getTranslation('name', 'es');
                return $en ?: $es;
            })->filter()->toArray();

            return [
                'is_relevant'     => true,
                'importance'      => 8,
                'is_seed'         => true,
                'source_language' => 'unknown', // will be inferred by redactBilingual from topic
                'category_name'   => $categories[0] ?? 'General',
                'facts'           => [$this->rawArticle->title],
            ];
        }

        // Get categories showing both EN and ES names for better matching
        $categories     = \App\Models\Category::active()->get()->map(function ($c) {
            $en = $c->getTranslation('name', 'en');
            $es = $c->getTranslation('name', 'es');
            return $en ? "{$en} / {$es}" : $es;
        })->filter()->toArray();
        $categoriesList = implode(', ', $categories) ?: 'General / General';

        $prompt = <<<PROMPT
You are a senior multilingual editorial AI. Analyze the following news article and respond in STRICT JSON only.

DATE: {$today}
VALID CATEGORIES: [{$categoriesList}]
ARTICLE TITLE: {$this->rawArticle->title}
ARTICLE CONTENT: {$content}

Detect the SOURCE LANGUAGE of the article automatically (it may be English, Spanish, French, Portuguese, or any other language).

Respond in STRICT JSON (no markdown):
{
    "is_relevant": true,
    "source_language": "en",
    "category_name": "AI / IA",
    "content_type": "news",
    "importance": 8,
    "facts": ["key fact 1", "key fact 2", "key fact 3"]
}

Rules:
- source_language: ISO 639-1 code of the article's source language (e.g., "en", "es", "pt", "fr")
- category_name: must match one of the valid categories above (use the English name part before the slash)
- content_type: one of: news, blog, guide, review, pillar
- importance: 1-10 based on editorial relevance
- facts: 3-7 concise key facts extracted from the article IN ENGLISH (always translate facts to English)
PROMPT;

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_GEMINI_LATEST);
        $result   = $this->parseJson($response);

        if ($result) {
            Log::info("RawArticle {$this->rawArticle->id} classified. Source language: " . ($result['source_language'] ?? 'unknown'));
        }

        return $result;
    }

    protected function redactBilingual(OpenRouterService $ai, array $classification, Author $author): ?array
    {
        $today          = now()->format('l, F j, Y');
        $isSeed         = $classification['is_seed'] ?? false;
        $contentType    = $classification['content_type'] ?? 'blog';
        $topic          = $isSeed ? $this->rawArticle->title : implode('; ', $classification['facts']);
        $sourceLang     = $classification['source_language'] ?? 'unknown';
        $sourceLangName = match($sourceLang) {
            'en'    => 'English',
            'es'    => 'Spanish',
            'pt'    => 'Portuguese',
            'fr'    => 'French',
            'de'    => 'German',
            'it'    => 'Italian',
            default => 'an automatically detected language',
        };

        $wordTargets = [
            'news'   => '1000-1500 words EN | 1000-1500 palabras ES',
            'blog'   => '1500-2000 words EN | 1500-2000 palabras ES',
            'guide'  => '1500-2500 words EN | 1500-2500 palabras ES',
            'review' => '1500-3000 words EN | 1500-3000 palabras ES',
            'pillar' => '2500-5000 words EN | 2500-5000 palabras ES',
        ];
        $wordTarget = $wordTargets[$contentType] ?? $wordTargets['blog'];


        $prompt = <<<PROMPT
You are a world-class bilingual Senior Print Journalist and elite SEO copywriter (15+ years experience) working for a premium tech publication.
DATE: {$today} | TYPE: {$contentType} | TARGET LENGTH: {$wordTarget}
SOURCE LANGUAGE OF THE RAW ARTICLE: {$sourceLangName} (ISO: {$sourceLang})
TOPIC (key facts already translated to English): {$topic}

IMPORTANT: The raw source article may be in {$sourceLangName}. You must ALWAYS produce the final article in BOTH English AND Spanish. This is mandatory — never skip either language.

=== MANDATORY QUALITY STANDARDS ===

SEDUCTIVE COPYWRITING & INTRIGUE:
- Craft magnetic headlines (title_en, title_es) that create an irresistible curiosity gap without being clickbait.
- The first paragraph MUST start with a hook (a bold statement, a surprising stat, or a rhetorical question) that grabs the reader instantly.
- Maintain an authoritative yet conversational tone (persuasive, intelligent, and engaging).

SEO - Google E-E-A-T (10/10 REQUIRED):
- Primary keyword in title (first 60 chars), first paragraph, at least 2 H2s, meta description.
- Use semantic LSI keywords naturally throughout.
- Title EN and ES: max 60 chars each.
- Excerpt/meta description EN and ES: persuasive, max 155 chars each. Formulate them as a teaser.
- Slug: lowercase-hyphens-no-special-chars (max 6 words each).

CONTENT ARCHITECTURE - Google Helpful Content:
- NO cliches: forbidden = "paradigm shift", "game-changer", "revolutionary", "cambio de paradigma", "en conclusión".
- BURSTINESS: alternate short punchy sentences (3-5 words) with complex analytical ones.
- STORYTELLING: Include 1 real or fictional micro-story/analogy per section to explain complex concepts cleanly.
- Use: H2 headings to break logic, <strong> for key facts, <blockquote> for crucial quotes or insights, <ul> or <ol> for readability.

ADA/WCAG 2.1 AAA ACCESSIBILITY:
- For images: ONLY place bare placeholder [IMAGE_1] on its own line. NO extra HTML around it.
- All image alt texts (in image_prompts) MUST describe the visual content vividly for a blind reader (not just keywords).
- Use semantic HTML strictly: h2, h3, p, ul, ol, blockquote, strong. Don't use bold just for styling.

IMAGERY - FLUX.1 Ultra-Realism:
- Generate 2 to 4 photorealistic image prompts.
- If humans appear: add "hyper-realistic skin textures, pores, authentic facial expression, natural lighting, 35mm DSLR Nikon D850, 8k resolution, cinematic composition, no text".

SELF-EVALUATION:
- Provide an unbiased SEO Score (1-100) based on your execution of keyword placement, readability, and structural optimization.

=== OUTPUT: STRICT JSON ONLY (no markdown, no text outside JSON) ===
{
    "title_en": "Compelling, curiosity-inducing English title (max 60 chars)",
    "title_es": "Titulo persuasivo y magnetico en Espanol (max 60 chars)",
    "slug_en": "english-seo-slug-max-6-words",
    "slug_es": "slug-seo-espanol-max-6-palabras",
    "excerpt_en": "Persuasive English teaser meta description (max 155 chars)",
    "excerpt_es": "Teaser meta descripcion persuasiva en Espanol (max 155 chars)",
    "keywords": ["primary keyword", "secondary kw", "lsi kw 1", "lsi kw 2"],
    "seo_score": 95,
    "content_en": "<p>Magnetic hook paragraph.</p><p>Full English HTML article. Place [IMAGE_1] on its own line between paragraphs.</p>",
    "content_es": "<p>Parrafo gancho magnetico.</p><p>Cuerpo del articulo en Espanol HTML. Coloca [IMAGE_1] en su propia linea entre parrafos.</p>",
    "image_prompts": [
        {
            "id": "[IMAGE_1]",
            "prompt_en": "Photojournalistic style, [specific scene], cinematic lighting, 35mm lens, 8k, no text.",
            "alt_en": "Vivid descriptive alt text for screen readers in English",
            "alt_es": "Texto alt descriptivo vivido para lectores de pantalla en Espanol",
            "caption_en": "Informative and engaging English caption",
            "caption_es": "Leyenda informativa y persuasiva en Espanol",
            "title_en": "SEO Keyword image title in English (max 70 chars)",
            "title_es": "Titulo SEO imagen en Espanol (max 70 chars)"
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
