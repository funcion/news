<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\User;
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

    public $timeout = 600; // 10 minutes — reasoning models (DeepSeek V4 Pro) take 3-5min per bilingual draft
    public $tries = 2;

    public function backoff(): array
    {
        return [30, 120];
    }

    public function __construct(
        protected RawArticle $rawArticle
    ) {}

    public function handle(OpenRouterService $ai, \App\Services\AI\SiliconFlowImageService $imageService, \App\Services\AI\TagGeneratorService $tagService, \App\Services\AI\DuplicateCheckerService $duplicateChecker): void
    {
        // Guard: require API key before processing
        if (empty(config('openai.api_key')) && empty(env('OPENROUTER_API_KEY'))) {
            Log::error("ProcessArticleWithAIJob: OPENROUTER_API_KEY is not set. Pausing job until configured.");
            $this->release(300); // retry in 5 minutes
            return;
        }

        $today = now()->format('l, F j, Y');
        Log::info("Processing RawArticle: {$this->rawArticle->id} (Bilingual EN/ES) at {$today}.");

        if ($this->rawArticle->status !== 'pending') {
            Log::warning("RawArticle {$this->rawArticle->id} already processed.");
            return;
        }

        // --- Guard against incomplete/failed previous attempts ---
        $existing = Article::where('raw_article_id', $this->rawArticle->id)->first();
        if ($existing) {
            $contentEn = $existing->getTranslation('content', 'en');
            $contentEs = $existing->getTranslation('content', 'es');
            
            if ($existing->status === 'published' && !empty($contentEn) && !empty($contentEs)) {
                Log::warning("RawArticle {$this->rawArticle->id} has already been fully processed and published (Article {$existing->id}). Skipping.");
                $this->rawArticle->update(['status' => 'processed']);
                return;
            }
            
            // If it exists but is incomplete (e.g. empty content or still draft), delete it so we can start fresh
            Log::info("Found incomplete/failed Article {$existing->id} for RawArticle {$this->rawArticle->id}. Deleting to retry fresh.");
            $existing->delete();
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

        // --- FILTER: Sensitive / Harmful Content ---
        if (!empty($classification['is_sensitive']) && $classification['is_sensitive'] === true) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::warning("RawArticle {$this->rawArticle->id} flagged as sensitive content. Blocked.");
            return;
        }

        // --- FILTER: Potentially False / Misinformation ---
        if (!empty($classification['is_potentially_false']) && $classification['is_potentially_false'] === true) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::warning("RawArticle {$this->rawArticle->id} flagged as potentially false/misinformation. Blocked.");
            return;
        }

        // --- FILTER: Article Age (null-safe for articles without source) ---
        $source = $this->rawArticle->source;
        $maxAgeDays = $source?->max_age_days ?? 7;
        if ($this->rawArticle->published_at && $this->rawArticle->published_at->lt(now()->subDays($maxAgeDays))) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::info("RawArticle {$this->rawArticle->id} rejected: article is {$this->rawArticle->published_at->diffForHumans()}, max age is {$maxAgeDays} days.");
            return;
        }

        // --- FILTER: Source Trust (null-safe — no source = trusted by default) ---
        if ($source && !$source->trusted && $source->score < 50) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::warning("RawArticle {$this->rawArticle->id} rejected: untrusted source with low score ({$source->score}).");
            return;
        }

        // --- NEW: DUPLICATE CHECK LEVEL 2 & 3 ---
        $isDuplicate = $duplicateChecker->checkAndHandleDuplicate(
             $this->rawArticle->title ?? '',
             $this->rawArticle->content ?? '',
             $this->rawArticle->url ?? '',
             $this->rawArticle->id
        );

        if ($isDuplicate) {
             $this->rawArticle->update(['status' => 'processed']);
             return;
        }

        $author = User::where('is_active', true)->inRandomOrder()->first() ?: User::first();

        if (!$author) {
            $author = User::create([
                'name'      => ['en' => 'Luis Figuera', 'es' => 'Luis Figuera'],
                'email'     => 'luis@glodaxia.com',
                'password'  => bcrypt(Str::random(16)),
                'slug'      => 'luis-figuera',
                'is_active' => true,
                'bio'       => [
                    'es' => '¡Hola! Soy Luis Figuera. Me especializo en escribir textos digitales y tradicionales, asegurando que cada palabra cumpla un objetivo comercial.',
                    'en' => 'Hello! I\'m Luis Figuera. I specialize in writing digital and traditional copy, ensuring that every word serves a commercial goal.',
                ],
            ]);
        }

        $redacted = $this->redactBilingual($ai, $classification, $author);

        if (!$redacted) {
            throw new \RuntimeException("AI could not draft bilingual content (attempt {$this->attempts()}).");
        }

        // --- AUTO-FIX: Truncate fields that exceed limits before validation ---
        $redacted = $this->autoFixRedactedOutput($redacted);

        // --- VALIDATE: Programmatic checks on AI output before creating Article ---
        $validationErrors = $this->validateRedactedOutput($redacted);
        if (!empty($validationErrors)) {
            Log::warning("redactBilingual validation failed for RawArticle {$this->rawArticle->id}", $validationErrors);
            throw new \RuntimeException(
                "AI output failed validation: " . implode('; ', $validationErrors) . " (attempt {$this->attempts()})"
            );
        }

        // --- CLEANUP: Remove AI hallucinated image attributes + inline URLs ---
        $contentEn = $this->cleanHallucinatedAttributes($redacted['content_en'] ?? '');
        $contentEs = $this->cleanHallucinatedAttributes($redacted['content_es'] ?? $contentEn);
        $contentEn = $this->cleanInlineUrls($contentEn);
        $contentEs = $this->cleanInlineUrls($contentEs);

        // Determine category — STRICT matching, no fallback to generic
        $categoryId = null;
        if (!empty($classification['category_name'])) {
            $matchedCat = \App\Models\Category::whereRaw("name->>'es' ILIKE ?", [trim($classification['category_name'])])->first()
                ?? \App\Models\Category::whereRaw("name->>'en' ILIKE ?", [trim($classification['category_name'])])->first();
            if ($matchedCat) {
                $categoryId = $matchedCat->id;
            }
        }

        // If no strict match, try partial match (contains)
        if (!$categoryId && !empty($classification['category_name'])) {
            $partialCat = \App\Models\Category::whereRaw("name->>'es' ILIKE ?", ['%' . trim($classification['category_name']) . '%'])->first()
                ?? \App\Models\Category::whereRaw("name->>'en' ILIKE ?", ['%' . trim($classification['category_name']) . '%'])->first();
            if ($partialCat) {
                $categoryId = $partialCat->id;
                Log::info("Category matched via partial search: {$partialCat->id}");
            }
        }

        // If STILL no match → reject to pending_review instead of publishing blindly
        if (!$categoryId) {
            Log::warning("RawArticle {$this->rawArticle->id}: No category match for '{$classification['category_name']}'. Setting to pending_review.");
            $categoryId = $source?->category_id ?? 1; // Use source default but flag it
            $this->rawArticle->update(['status' => 'processed']);
            // Article will be created as draft (not published) so admin can review
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
        $article->user_id        = $author->id;
        $article->category_id    = $categoryId;
        $article->status         = 'draft'; // Keep as draft during processing so it is hidden from the public frontend until complete
        $article->published_at   = now();
        $article->seo_score      = 85; // Static default — self-reported AI scores are unreliable, use Filament for manual override
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

                    // --- NEW: Featured image ([IMAGE_1]) should NOT be in the body content ---
                    if ($placeholder !== '[IMAGE_1]') {
                        $contentEn = str_replace($placeholder, $imgTagEn, $contentEn);
                        $contentEs = str_replace($placeholder, $imgTagEs, $contentEs);
                    } else {
                        // Safety: remove [IMAGE_1] if AI placed it in content anyway
                        $contentEn = str_replace($placeholder, '', $contentEn);
                        $contentEs = str_replace($placeholder, '', $contentEs);
                    }

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
                        $article->image_url = $article->getBestImageUrl('images_en', 'large');
                        $article->save();
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
                    Log::warning("Image generation failed for placeholder {$placeholder}.");

                    // --- HARD STOP: Featured image [IMAGE_1] is mandatory ---
                    if ($placeholder === '[IMAGE_1]') {
                        $article->delete(); // rollback article creation
                        throw new \RuntimeException(
                            "Featured image generation failed for RawArticle {$this->rawArticle->id}. Article rolled back."
                        );
                    }
                }
            }
        }

        // --- SAFETY NET: No images at all → abort ---
        if ($imageCount === 0) {
            $article->delete();
            throw new \RuntimeException(
                "No images were generated for RawArticle {$this->rawArticle->id}. Article rolled back."
            );
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

        $article->status = 'published'; // Set to published now that drafting and image injection is complete
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
            try {
                event(new \App\Events\ArticlePublished($article));
            } catch (\Exception $e) {
                Log::error("Realtime event broadcasting failed for Article {$article->id}: " . $e->getMessage());
            }
        }

        // --- Sitemap: flush cache + IndexNow ping ---
        try {
            \App\Http\Controllers\SitemapController::flushCache();
            $articleUrl = url('/' . $article->slug_en);
            \App\Http\Controllers\IndexNowController::ping($articleUrl);
        } catch (\Exception $e) {
            Log::warning("Sitemap/IndexNow failed for Article {$article->id}: " . $e->getMessage());
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

    /**
     * Strip any inline external URLs from the HTML content.
     * The AI is instructed not to place URLs, but safety net catches any
     * that slip through. References are stored in ai_metadata['origin_url'].
     */
    private function cleanInlineUrls(string $content): string
    {
        // Remove <a href="...">...</a> links — keep the visible text
        $content = preg_replace('/<a\s+[^>]*href=["\']https?:\/\/[^"\']*["\'][^>]*>(.*?)<\/a>/is', '$1', $content);
        // Remove bare https://... URLs (not inside tags)
        $content = preg_replace('/(?<!["\'=])https?:\/\/[^\s<>"\')\]]+/', 'una fuente verificada', $content);
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
        $candidate = $attempt === 0 ? $slug : "{$slug}-" . ($attempt + 1);
        
        // We must check BOTH columns to ensure a slug is truly unique across the whole site
        $exists = Article::where('slug_en', $candidate)
            ->orWhere('slug_es', $candidate)
            ->exists();

        if ($exists) {
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

        $sourceTrusted = ($source && $source->trusted) ? 'YES' : 'NO';
        $sourceScore   = $source?->score ?? 0;
        $articleAge    = $this->rawArticle->published_at ? $this->rawArticle->published_at->diffForHumans() : 'unknown';

        $prompt = <<<PROMPT
You are a senior multilingual editorial AI. Analyze the following news article and respond in STRICT JSON only.

DATE: {$today}
VALID CATEGORIES: [{$categoriesList}]
ARTICLE TITLE: {$this->rawArticle->title}
ARTICLE CONTENT: {$content}
SOURCE TRUSTED: {$sourceTrusted} (score: {$sourceScore})
ARTICLE AGE: {$articleAge}

Detect the SOURCE LANGUAGE of the article automatically (it may be English, Spanish, French, Portuguese, or any other language).

Respond in STRICT JSON (no markdown):
{
    "is_relevant": true,
    "is_sensitive": false,
    "is_potentially_false": false,
    "source_language": "en",
    "category_name": "AI / IA",
    "content_type": "news",
    "importance": 8,
    "facts": ["key fact 1", "key fact 2", "key fact 3"]
}

Rules:
- source_language: ISO 639-1 code of the article's source language (e.g., "en", "es", "pt", "fr")
- category_name: MUST exactly match one of the VALID CATEGORIES listed above. Use the English name part before the slash. If NONE of the valid categories fit, set is_relevant to false.
- content_type: one of: news, blog, guide, review, pillar
- importance: 1-10 based on editorial relevance
- facts: 3-7 concise key facts extracted from the article IN ENGLISH (always translate facts to English)
- is_sensitive: set to TRUE if the content involves: graphic violence, hate speech, explicit sexual content, illegal activities, self-harm, terrorism, or content that could cause legal liability
- is_potentially_false: set to TRUE if the article contains obvious misinformation, fabricated statistics, conspiracy theories, unverified claims presented as fact, or reads like propaganda/sponsored content disguised as news
PROMPT;

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_ACTIVE);
        $result   = $this->parseJson($response);

        if ($result) {
            Log::info("RawArticle {$this->rawArticle->id} classified. Source language: " . ($result['source_language'] ?? 'unknown'));
        }

        return $result;
    }

    protected function redactBilingual(OpenRouterService $ai, array $classification, User $author): ?array
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

        $wordTargets = config('global.editorial.word_targets');
        $wordTarget = $wordTargets[$contentType] ?? $wordTargets['blog'];

        $authorNameEn = $author->getTranslation('name', 'en') ?: $author->getTranslation('name', 'es') ?: $author->name;
        $authorBioEn  = $author->getTranslation('bio', 'en') ?: $author->getTranslation('bio', 'es') ?: $author->bio;

        $persona = config('global.editorial.persona');
        $rules   = config('global.editorial.focus_rules');

        // Generate randomized style DNA — 9,216,000+ unique macro-combinations per article
        $styleDna       = $this->generateStyleDNA();
        $paragraphRules = $styleDna['paragraphRules'];
        $temperature    = $styleDna['temperature'];

        $prompt = <<<PROMPT
You are a {$persona}. Your job: write a compelling, deeply human OPINION COLUMN.

DATE: {$today} | TYPE: {$contentType} | TARGET: {$wordTarget}
SOURCE LANGUAGE: {$sourceLangName} (ISO: {$sourceLang})
VERIFIED FACTS FROM SOURCE: {$topic}

NON-NEGOTIABLE: {$rules}
NON-NEGOTIABLE: You MUST produce the final article in BOTH English AND Spanish.

═══ 0. RANDOMIZATION SEEDS (MANDATORY — SELECT BEFORE WRITING) ═══

These seeds control macro-variation across millions of articles. You MUST follow the behavioral rules assigned to each seed.

- STYLE_SEED: {$styleDna['styleSeed']}
- STRUCTURE_VARIANT: {$styleDna['structureVariant']}
- HOOK_TYPE: {$styleDna['hookType']}
- IMAGE_PLACEMENT: {$styleDna['imagePlacement']}
- OPENING_STRATEGY: {$styleDna['openingStrategy']}
- ANALOGY_DOMAIN: {$styleDna['analogyDomain']}
- HUMAN_NOISE: {$styleDna['humanNoise']}
- TONE_BLEND: {$styleDna['toneBlend']}

═══ 1. VOICE & AUTHENTICITY (HIGHEST PRIORITY) ═══

AUTHOR PERSONA:
- Name: {$authorNameEn}
- Bio/Background: {$authorBioEn}

You MUST adopt the professional persona, opinions, and expertise of {$authorNameEn} based on their background. 

STYLE_SEED BEHAVIOR (follow your assigned STYLE_SEED from Section 0):
- skeptical_analyst: shorter paragraphs (avg 2-3 sentences), heavy on data references, zero humor, dry and precise
- provocative_essayist: open with a contrarian take, use exactly 1 strong metaphor, address the reader directly, never hedge
- warmer_storyteller: open with a small real-world observation, simpler vocabulary, allow "we" consistently, warmer tone
- cold_forensic: impersonal, evidence-first, no first-person until the close, clinical precision
- enthusiastic_rebel: high energy, short punchy sentences, challenge the status quo, use "Look," or "Here's the thing,"
- weary_insider: tired but knowledgeable, "I've seen this before" energy, reluctant conclusions, industry jargon allowed

TONE_BLEND (follow your assigned TONE_BLEND — this is your dominant tonal register, not the only one):
- skeptical_and_sharp: dry humor, short sentences, data-heavy
- measured_and_authoritative: calm, confident, occasional curiosity cracks
- quietly_intense: building pressure, evidence stacking, verdict-like close
- conversational_but_precise: explaining to a smart friend, natural rhythm
- urgently_investigative: discovered something, laying it out fast
- analytically_cold_with_bursts: mostly clinical, with 1-2 moments of warmth
- provocative_and_combative: challenging the reader, then pulling back fair
- reflective_and_layered: thinking in real time, not presenting finished opinions

TAKE A CLEAR STANCE. Every column needs a clear, opinionated thesis: is this overhyped? Dangerous? Quietly brilliant? Reckless? State it precisely where your assigned STRUCTURE_VARIANT mandates it. Commit fully. A columnist who hedges on everything is a columnist nobody reads. When claims from the source are unverified, flag them honestly ("reports suggest", "if confirmed") — but your analytical OPINION about those claims must still be sharp and decisive.

MANDATORY ANCHORS OF REALITY (NON-NEGOTIABLE):
- UNCERTAINTY: Include exactly {$paragraphRules['doubt_moments']} sentence(s) in each language where you openly doubt part of your own argument, admit a gap in knowledge, or hedge on a prediction. Vary the phrasing each time — never use the same doubt structure twice.
- NO EXTERNAL URLS: NEVER include URLs or hyperlinks inside content_en or content_es. Cite sources by name only.
- PERSONAL GROUNDING: Write in first person ("I", "we") with at least ONE moment grounded in your professional lens. VARY how you do it. Never open with "In my experience". Instead: a pattern you noticed over time, a moment of realization, something you covered before, your reaction when first reading this news. Make it feel remembered, not performed.
- HYPER-SPECIFIC DATA: Be as specific as possible — exact percentages, named sources, timeframes. If you lack the exact figure, use a qualified approximate ("roughly 40%", "industry estimates suggest"). NEVER invent a fake statistic. A vague but honest estimate reads as more human than a suspiciously precise fabrication.

WRITE WITH GENUINE PERSONALITY:
- Use contractions naturally: "don't", "isn't", "we've", "they're"
- Use em dashes — like this — for dramatic asides or mid-thought clarifications
- Strong, opinionated adjectives: "reckless", "lazy", "alarming", "brilliant" — never safe filler like "interesting" or "notable"
- Challenge PR spin and vague marketing language with specific counter-evidence

PARAGRAPH ASYMMETRY (the #1 AI fingerprint — break it or get flagged):
- Include exactly {$paragraphRules['single_sentence']} paragraph(s) of 1 sentence (5-12 words) for rhetorical punch
- Include exactly {$paragraphRules['long_paragraphs']} paragraph(s) of 6-8 sentences for deep analysis
- Include exactly {$paragraphRules['fragment_paragraphs']} paragraph(s) that are a single word or very short fragment on their own line
- All remaining paragraphs: vary between 2, 3, and 4 sentences — NEVER write two consecutive paragraphs with the same sentence count

SENTENCE RHYTHM:
- Mix 6-word punches with 30-word analytical sentences within the same paragraph
- NEVER start 3+ consecutive sentences with the same grammatical structure
- Occasionally start a sentence with "And" or "But" — real writers do this constantly

ANALOGY RULES (use your assigned ANALOGY_DOMAIN from Section 0):
- Do NOT default to food, sports, or travel — these are overused AI crutches
- Include exactly {$paragraphRules['analogies']} analogy/analogies from your ANALOGY_DOMAIN
- If you catch yourself writing "like a recipe for" or "home run" or "low-hanging fruit" — delete it and use your ANALOGY_DOMAIN

HUMAN NOISE (follow your assigned HUMAN_NOISE from Section 0):
- Include exactly {$paragraphRules['human_noise']} moment(s) of human noise: brief digression, half-formed thought, question left hanging, "okay, fair enough"
- These are the texture of real writing. More than {$paragraphRules['human_noise']} reads as try-hard.

═══ 2. ANTI-AI DETECTION PATTERNS (CRITICAL) ═══

These are the structural tells that flag machine-generated text. NEVER DO THEM:
- Open mid-article paragraphs with rhetorical questions ("But what does this mean for X?")
- Use balanced "on one hand / on the other hand" framing in every section
- Start 3+ consecutive sentences with identical syntactic pattern (Subject-Verb-Object)
- End sections with hollow wrap-up lines ("This will certainly be worth watching", "Only time will tell")
- Chain 3+ consecutive single-sentence paragraphs for artificial dramatic effect
- Repeat the same analogy or metaphor twice in one article
- Start an article with a sweeping generalization about "the state of" or "in today's rapidly evolving"

BLOCKED PHRASES (AI fingerprints — using ANY of these fails the article):
EN: "paradigm shift", "game-changer", "revolutionary", "democratization of", "inflection point", "trajectory points toward", "unprecedented scale", "seamlessly integrate", "robust ecosystem", "the digital landscape", "it remains to be seen", "only time will tell", "it's worth noting", "in today's rapidly evolving", "at the end of the day", "raises important questions", "a bold step forward", "double-edged sword", "the implications are profound", "a testament to"
ES: "cambio de paradigma", "en conclusión", "sin lugar a dudas", "cabe destacar", "queda por ver", "un arma de doble filo", "marca un antes y un después", "las implicaciones son profundas"

OPENING FINGERPRINTS (never start with these — instant AI detection):
- "In today's [adjective] [noun]..." / "En el [adjetivo] mundo de..."
- "Let's dive in" / "Let me break this down"
- "I've been covering X for Y years and..."
- "Picture this:" / "Imagine a world where..."
- "If you've been following [topic]..."
- "In my experience" as an opener
- Any sweeping generalization about "the state of" a whole industry

BLOCKED STRUCTURAL PATTERNS (structural, not lexical):
- Never start 3+ consecutive paragraphs with the same number of words in the first sentence
- Never use the same transition word (But/However/Still/Yet) more than twice in the entire article
- Never end two consecutive sections with a single-sentence paragraph
- Never make all H2 headings exactly the same word count (4-6 words each is a tell)
- The last paragraph must NEVER start with "In the end", "Ultimately", "At the end of the day"

FOOD/SPORT METAPHOR LIMIT:
- Maximum ONE food or sports metaphor per article. Zero is fine.
- If you write "like a recipe for" or "home run" or "low-hanging fruit" — delete it and use your ANALOGY_DOMAIN.

═══ 3. ARTICLE STRUCTURE (follow your STRUCTURE_VARIANT from Section 0) ═══

ALLOWED HTML TAGS ONLY: <p>, <h2>, <strong>, <blockquote>, <ul>, <ol>, <li>. NEVER use <h1>, <h3>, <h4>, <div>, <span>, or markdown bold (**) inside HTML content.

STRUCTURE_VARIANT RULES (pick the one assigned to you):
- classic_hook_thesis_body_close: Hook (1-2 sentences, concrete fact) → Thesis (paragraph 2, clear stance) → Body (2-3 H2 sections) → Close (prediction or provocation)
- anecdote_first_then_takeaway: Start with a 2-3 sentence real-world observation or scenario → reveal your thesis in paragraph 3 → Body → Close
- question_opening_no_answer_until_middle: Open with a direct question to the reader → delay your actual stance until after the first H2 → build tension → Close with your answer
- prediction_top_analysis_bottom: State your bold prediction in paragraph 2 → spend the rest proving or defending it → Close with implications
- counterintuitive_lead_evidence_later: Open with "Everyone thinks X. They're wrong." or equivalent → delay evidence until after first H2 → build the case → Close with a warning

ALL VARIANTS share these rules:
- You MUST alternate text blocks with structural image tokens. No more than two consecutive paragraphs without an [IMAGE_N] token on its own standalone line.
- Use <strong> for key data points, <blockquote> for critical insights or notable quotes, <ul>/<ol> for scannable information.
- H2 headings must vary in length — never make them all 4-6 words. Mix short ("Why?") with long ("The Hidden Cost Nobody's Talking About").
- Use your assigned HOOK_TYPE for the opening:
  - number_fact: lead with a specific number, date, or statistic
  - quote: lead with a real quote from the source or a relevant public figure
  - question: lead with a direct question (not a cliché rhetorical one)
  - scene_setting: describe a specific real place or moment ("In a cramped office in Austin last Tuesday...")
  - confession: open with a candid admission ("I have to admit: I was wrong about X.")

═══ 4. SEO & E-E-A-T ═══

- Title: max 60 chars, primary keyword naturally integrated. Must spark genuine curiosity.
- Meta title: max 70 chars, SEO-optimized variant of the title.
- Excerpt: max 155 chars, a teaser that creates urgency — not a summary.
- Slug: lowercase-hyphenated, max 6 words.
- Primary keyword must appear in: title, first paragraph, at least 1 H2, and excerpt.
- Weave semantic LSI keywords naturally. Never stuff.
- Schema.org JSON-LD: NewsArticle with accurate date, real author name, and description.

═══ 5. IMAGE PLACEMENT (CRITICAL — READ CAREFULLY) ═══

Generate 3 to 5 photorealistic image prompts (FLUX.1 style: 35mm DSLR Nikon D850, cinematic natural lighting, shallow depth of field, 8k resolution, hyper-realistic, no text overlay, no watermark).

- [IMAGE_1] = Hero/featured image ONLY. Do NOT place [IMAGE_1] inside content_en or content_es.
- [IMAGE_2], [IMAGE_3], etc. = Interior images. You MUST place the literal string token (e.g., [IMAGE_2]) on its OWN STANDALONE LINE between paragraphs. NEVER replace the token with the caption text or a quote inside the content_en/content_es strings. The token string must appear raw and intact so the backend parser can replace it. Never embed them inside a <p> tag. Never skip them.
- SYNCHRONIZATION RULE: Every [IMAGE_N] token placed in content_en/content_es MUST have a corresponding object in the "image_prompts" array with the exact same "id". Never place an image token without its prompt object. Never generate a prompt object without using its token in the text.
- Alt text: descriptive and concise, max 125 characters each.
- If humans appear in the image: add "hyper-realistic skin textures, authentic facial expression" to the prompt.
- IMAGE PLACEMENT (follow your IMAGE_PLACEMENT from Section 0):
  - both_early: place [IMAGE_2] and [IMAGE_3] in the first half of the article body
  - both_late: place [IMAGE_2] and [IMAGE_3] in the second half of the article body
  - one_early_one_late: place [IMAGE_2] early (before first H2 or just after) and [IMAGE_3] late (near the close)
  - scattered: distribute images evenly — one after each H2 section
  - NEVER place images in the exact same relative position across consecutive articles

═══ 6. BILINGUAL REQUIREMENT ═══

The Spanish version must NOT be a literal translation. It must read as if a native Spanish-speaking columnist wrote it independently — with natural phrasing, idiomatic expressions, and equivalent rhetorical impact. Both versions must feel like premium journalism.

═══ OUTPUT: STRICT JSON ONLY (no markdown fences, no commentary, no text outside the JSON object) ═══

CRITICAL JSON FORMATTING RULES:
1. Inside HTML content strings, use single quotes (') for speech or attribute quotes. NEVER use unescaped double quotes (") inside text values — they break JSON parsing.
2. Use literal "\n" for newlines inside content strings. Do NOT insert actual line breaks inside a JSON string value.
3. Only use the allowed HTML tags listed above (<p>, <h2>, <strong>, <blockquote>, <ul>, <ol>, <li>).
4. Every interior image token must be isolated like this: `\n\n[IMAGE_2]\n\n` inside the HTML content string. Do not append text or caption sentences to that line.

{
    "title_en": "Punchy headline with primary keyword (max 60 chars)",
    "title_es": "Titular magnetico con palabra clave (max 60 chars)",
    "slug_en": "seo-slug-max-six-words",
    "slug_es": "slug-seo-espanol-max-seis",
    "excerpt_en": "Compelling teaser, not a summary (max 155 chars)",
    "excerpt_es": "Teaser persuasivo, no un resumen (max 155 chars)",
    "meta_title_en": "SEO optimized title variant (max 70 chars)",
    "meta_title_es": "Titulo optimizado SEO variante (max 70 chars)",
    "keywords": ["primary keyword", "secondary kw", "lsi term 1", "lsi term 2"],
    "content_en": "Start with the opening hook based on your assigned HOOK_TYPE and STRUCTURE_VARIANT. Build the full HTML column here following all injected rules. Use only allowed tags. Place [IMAGE_N] tokens on standalone lines. Follow ASYMMETRY values.",
    "content_es": "Comenzar con el hook segun HOOK_TYPE y STRUCTURE_VARIANT asignados. Construir la columna HTML completa siguiendo todas las reglas inyectadas. Usar solo tags permitidos. Colocar [IMAGE_N] en lineas independientes.",
    "image_prompts": [
        {
            "id": "[IMAGE_1]",
            "prompt_en": "Photojournalistic style, [specific visual scene directly related to the article topic], shot on 35mm Nikon D850, cinematic natural lighting, shallow depth of field, 8k resolution, no text overlay, no watermark",
            "alt_en": "Descriptive alt text (max 125 chars)",
            "alt_es": "Texto alt descriptivo (max 125 chars)",
            "caption_en": "Caption connecting the image to the article context",
            "caption_es": "Leyenda conectando la imagen con el contexto del articulo",
            "title_en": "SEO image title (max 70 chars)",
            "title_es": "Titulo SEO de imagen (max 70 chars)"
        },
        {
            "id": "[IMAGE_2]",
            "prompt_en": "Photojournalistic style, [different scene supporting the argument], 35mm lens, natural lighting, 8k, no text",
            "alt_en": "Alt text (max 125 chars)",
            "alt_es": "Texto alt (max 125 chars)",
            "caption_en": "Contextual caption",
            "caption_es": "Leyenda contextual",
            "title_en": "Image title (max 70 chars)",
            "title_es": "Titulo imagen (max 70 chars)"
        },
        {
            "id": "[IMAGE_3]",
            "prompt_en": "Photojournalistic style, [third visual angle on the topic], 35mm lens, cinematic composition, 8k, no text",
            "alt_en": "Alt text (max 125 chars)",
            "alt_es": "Texto alt (max 125 chars)",
            "caption_en": "Contextual caption",
            "caption_es": "Leyenda contextual",
            "title_en": "Image title (max 70 chars)",
            "title_es": "Titulo imagen (max 70 chars)"
        }
    ],
    "json_ld": {
        "@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": "Same as title_en",
        "datePublished": "{$today}",
        "author": {"@type": "Person", "name": "{$author->name}"},
        "description": "Same as excerpt_en"
    }
}

FINAL SELF-CHECK (verify ONLY these 5 — the rest is validated programmatically by the backend):
1. [IMAGE_N] tokens on their own lines in BOTH content_en AND content_es
2. [IMAGE_1] NOT inside content strings
3. Zero phrases from BLOCKED PHRASES or OPENING FINGERPRINTS lists
4. Spanish reads as native, not translated
5. STRUCTURE_VARIANT from Section 0 is followed
PROMPT;

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_ACTIVE, ['temperature' => $temperature]);
        if (!$response) {
            Log::warning("redactBilingual: AI returned null response for RawArticle {$this->rawArticle->id} (likely timeout)");
            return null;
        }
        $data     = $this->parseJson($response);

        if (!$data) {
            Log::warning("redactBilingual: AI returned invalid JSON for RawArticle {$this->rawArticle->id}", [
                'response_preview' => substr($response, 0, 500),
            ]);
            return null;
        }

        if (isset($data['keywords']) && is_string($data['keywords'])) {
            $data['keywords'] = array_map('trim', explode(',', $data['keywords']));
        }

        return $data;
    }

    protected function parseJson(?string $json): ?array
    {
        if (!$json) return null;

        // Remove <think> blocks (reasoning models like DeepSeek R1, Qwen3)
        $clean = preg_replace('~<think>.*?</think>~s', '', $json);

        // Remove markdown code fences (multiline-safe)
        $clean = preg_replace('~^```json\s*|\s*```$~m', '', $clean);
        $clean = preg_replace('~^```\s*|\s*```$~m', '', $clean);

        // Extract only the JSON portion between first { and last }
        $start = strpos($clean, '{');
        $end   = strrpos($clean, '}');
        if ($start === false || $end === false || $end <= $start) {
            Log::warning("parseJson: no JSON object found in response", ['raw' => substr($json, 0, 300)]);
            return null;
        }
        $clean = substr($clean, $start, $end - $start + 1);

        $result = json_decode(trim($clean), true);

        // If still failing, attempt repair of common AI JSON mistakes
        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            Log::warning("parseJson: initial decode failed, attempting repair", [
                'error' => json_last_error_msg(),
                'preview' => substr($clean, 0, 500),
            ]);

            $repaired = $clean;

            // 1. Escape unescaped control characters (newlines, tabs) inside string literals
            $repaired = preg_replace_callback('/"(?:[^"\\\\]|\\\\.)*"/s', function ($matches) {
                $str = $matches[0];
                $inner = substr($str, 1, -1);
                // Replace actual raw carriage returns and newlines with literal "\n"
                $inner = str_replace(["\r\n", "\r", "\n"], '\n', $inner);
                // Replace raw tabs with literal "\t"
                $inner = str_replace("\t", '\t', $inner);
                return '"' . $inner . '"';
            }, $repaired);

            // 2. Fix trailing commas before closing braces/brackets
            $repaired = preg_replace('/,\s*}/', '}', $repaired);
            $repaired = preg_replace('/,\s*]/', ']', $repaired);

            $result = json_decode(trim($repaired), true);

            if ($result) {
                Log::info("parseJson: repair succeeded");
            } else {
                Log::warning("parseJson: JSON decode failed after repair", [
                    'error' => json_last_error_msg(),
                    'raw' => substr($repaired, 0, 500),
                ]);
            }
        }

        return $result ?: null;
    }

    /**
     * Programmatic validation of AI-generated content.
     * Returns empty array if valid, or array of error messages.
     */
    protected function validateRedactedOutput(array $data): array
    {
        $errors = [];

        $contentEn = $data['content_en'] ?? '';
        $contentEs = $data['content_es'] ?? '';

        // 1. [IMAGE_1] must NOT appear inside content
        if (str_contains($contentEn, '[IMAGE_1]')) {
            $errors[] = '[IMAGE_1] found inside content_en (should only be in image_prompts)';
        }
        if (str_contains($contentEs, '[IMAGE_1]')) {
            $errors[] = '[IMAGE_1] found inside content_es (should only be in image_prompts)';
        }

        // 2. Every [IMAGE_N] in content must have a matching image_prompts entry
        $promptIds = collect($data['image_prompts'] ?? [])->pluck('id')->toArray();
        preg_match_all('/\[IMAGE_(\d+)\]/', $contentEn, $matchesEn);
        foreach ($matchesEn[0] ?? [] as $token) {
            if (!in_array($token, $promptIds)) {
                $errors[] = "Token {$token} in content_en has no matching image_prompts entry";
            }
        }

        // 3. Image tokens in EN must also exist in ES (synchronized placement)
        foreach ($matchesEn[0] ?? [] as $token) {
            if (!str_contains($contentEs, $token)) {
                $errors[] = "Token {$token} exists in content_en but missing from content_es";
            }
        }

        // 4. Must have image_prompts with at least [IMAGE_1]
        if (!in_array('[IMAGE_1]', $promptIds)) {
            $errors[] = 'Missing [IMAGE_1] in image_prompts array (hero image required)';
        }

        // 5. Title/excerpt length — AUTO-FIXED by autoFixRedactedOutput(), skip validation

        // 6. Content must not be empty
        if (strlen(strip_tags($contentEn)) < 200) {
            $errors[] = 'content_en is too short (less than 200 chars stripped)';
        }
        if (strlen(strip_tags($contentEs)) < 200) {
            $errors[] = 'content_es is too short (less than 200 chars stripped)';
        }

        // 7. Check for blocked AI-fingerprint phrases
        $blockedPhrases = [
            'paradigm shift', 'game-changer', 'revolutionary', 'democratization of',
            'inflection point', 'trajectory points toward', 'unprecedented scale',
            'seamlessly integrate', 'robust ecosystem', 'the digital landscape',
            'it remains to be seen', 'only time will tell', 'it\'s worth noting',
            'in today\'s rapidly evolving', 'at the end of the day',
            'raises important questions', 'a bold step forward', 'double-edged sword',
            'the implications are profound', 'a testament to',
            'let\'s dive in', 'let me break this down', 'in my experience',
            'low-hanging fruit', 'home run', 'slam dunk', 'picture this',
        ];
        $contentEnLower = strtolower($contentEn);
        foreach ($blockedPhrases as $phrase) {
            if (str_contains($contentEnLower, $phrase)) {
                $errors[] = "Blocked AI-fingerprint phrase detected in content_en: '{$phrase}'";
                break; // One is enough to flag
            }
        }

        return $errors;
    }

    /**
     * Auto-fix fields that exceed limits. Runs BEFORE validation.
     * Truncates titles, excerpts, meta fields. Logs what was fixed.
     */
    protected function autoFixRedactedOutput(array $data): array
    {
        $fixes = [];

        // Truncate titles (max 60 chars, cut at last word boundary)
        foreach (['title_en', 'title_es'] as $field) {
            if (mb_strlen($data[$field] ?? '') > 60) {
                $original = $data[$field];
                $data[$field] = Str::limit($data[$field], 60, '');
                // Cut at last space to avoid mid-word truncation
                if (($lastSpace = mb_strrpos($data[$field], ' ')) !== false && $lastSpace > 40) {
                    $data[$field] = mb_substr($data[$field], 0, $lastSpace);
                }
                $fixes[] = "{$field}: truncated from " . mb_strlen($original) . " to " . mb_strlen($data[$field]) . " chars";
            }
        }

        // Truncate excerpts (max 155 chars)
        foreach (['excerpt_en', 'excerpt_es'] as $field) {
            if (mb_strlen($data[$field] ?? '') > 155) {
                $data[$field] = Str::limit($data[$field], 155, '');
                $fixes[] = "{$field}: truncated to 155 chars";
            }
        }

        // Truncate meta titles (max 70 chars)
        foreach (['meta_title_en', 'meta_title_es'] as $field) {
            if (mb_strlen($data[$field] ?? '') > 70) {
                $data[$field] = Str::limit($data[$field], 70, '');
                $fixes[] = "{$field}: truncated to 70 chars";
            }
        }

        // Truncate meta descriptions (max 160 chars)
        foreach (['meta_description_en', 'meta_description_es'] as $field) {
            if (mb_strlen($data[$field] ?? '') > 160) {
                $data[$field] = Str::limit($data[$field], 160, '');
                $fixes[] = "{$field}: truncated to 160 chars";
            }
        }

        if (!empty($fixes)) {
            Log::info('autoFixRedactedOutput: ' . implode(', ', $fixes));
        }

        return $data;
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

    /**
     * Generate randomized style DNA for each article.
     * 6 x 5 x 5 x 4 x 10 x 12 x 8 x 8 = 9,216,000 unique macro-combinations.
     * Each call shuffles arrays and picks one option per dimension.
     */
    private function generateStyleDNA(): array
    {
        $styleSeeds = [
            'skeptical_analyst',
            'provocative_essayist',
            'warmer_storyteller',
            'cold_forensic',
            'enthusiastic_rebel',
            'weary_insider',
        ];

        $structureVariants = [
            'classic_hook_thesis_body_close',
            'anecdote_first_then_takeaway',
            'question_opening_no_answer_until_middle',
            'prediction_top_analysis_bottom',
            'counterintuitive_lead_evidence_later',
        ];

        $hookTypes = [
            'number_fact',
            'quote',
            'question',
            'scene_setting',
            'confession',
        ];

        $imagePlacements = [
            'both_early',
            'both_late',
            'one_early_one_late',
            'scattered',
        ];

        $openings = [
            'Start with a specific number, statistic, or date from the source',
            'Start mid-scene: describe something already happening',
            'Start with a blunt declarative claim (3-8 words) that takes a stance',
            'Start with the least obvious detail from the source facts',
            'Start by naming a specific entity and what they just did',
            'Start with a contradiction of the common assumption',
            'Start with a sensory or practical detail',
            'Start with a question you immediately answer',
            'Start in the middle of an argument, as if the reader walked in on you thinking',
            'Start with an unexpected comparison, then pivot within 2 sentences',
        ];

        $analogyDomains = [
            'historical events with specific dates (e.g., the 1929 crash, the Berlin Wall)',
            'scientific processes (e.g., how antibodies work, plate tectonics)',
            'architecture and engineering (e.g., building a bridge, structural failure)',
            'legal cases and regulatory history (e.g., antitrust rulings, patent disputes)',
            'military strategy and logistics (e.g., supply lines, flanking maneuvers)',
            'medical diagnosis and treatment (e.g., triage, differential diagnosis)',
            'ecological systems (e.g., predator-prey dynamics, invasive species)',
            'financial markets (e.g., arbitrage, liquidity crises, compound interest)',
            'music composition (e.g., counterpoint, improvisation, key changes)',
            'cinema and storytelling (e.g., foreshadowing, unreliable narrators)',
            'manufacturing and quality control (e.g., assembly lines, bottlenecks)',
            'navigation and cartography (e.g., dead reckoning, uncharted territory)',
        ];

        $humanNoise = [
            'Second-guess your own thesis briefly, then recover with stronger evidence',
            'Address someone who might disagree — a real counterpoint, not a strawman',
            'Include one aside unrelated to the topic, then snap back',
            'Admit you do not have the full picture, and that bothers you',
            'Show a moment of genuine emotional reaction — surprise, frustration, admiration',
            'Start a sentence mid-thought, as if already thinking before writing',
            'Ask one rhetorical question you do NOT immediately answer',
            'Reference a private professional habit (checking a dashboard, a morning routine)',
        ];

        $toneBlends = [
            'skeptical_and_sharp',
            'measured_and_authoritative',
            'quietly_intense',
            'conversational_but_precise',
            'urgently_investigative',
            'analytically_cold_with_bursts',
            'provocative_and_combative',
            'reflective_and_layered',
        ];

        shuffle($styleSeeds);
        shuffle($structureVariants);
        shuffle($hookTypes);
        shuffle($imagePlacements);
        shuffle($openings);
        shuffle($analogyDomains);
        shuffle($humanNoise);
        shuffle($toneBlends);

        // Probabilistic paragraph rules — each article gets different counts (no "EXACTLY ONE" fingerprint)
        $paragraphRules = [
            'single_sentence'     => random_int(1, 3),
            'long_paragraphs'     => random_int(0, 2),
            'fragment_paragraphs' => random_int(0, 2),
            'analogies'           => random_int(1, 2),
            'doubt_moments'       => random_int(1, 3),
            'human_noise'         => random_int(1, 3),
        ];

        // Temperature per seed — colder for forensic, hotter for rebel, with jitter
        $temperatureMap = [
            'skeptical_analyst'    => 0.6,
            'provocative_essayist' => 0.8,
            'warmer_storyteller'   => 0.75,
            'cold_forensic'        => 0.45,
            'enthusiastic_rebel'   => 0.85,
            'weary_insider'        => 0.65,
        ];
        $temperature = ($temperatureMap[$styleSeeds[0]] ?? 0.7) + (mt_rand(-5, 5) / 100);
        $temperature = max(0.3, min(1.0, $temperature));

        // Compatibility matrix — resolve hookType AND toneBlend conflicts per seed
        $compatMatrix = [
            'cold_forensic' => [
                'forbidden_hooks' => ['confession'],
                'allowed_hooks'   => ['number_fact', 'quote', 'scene_setting'],
                'allowed_tones'   => ['analytically_cold_with_bursts', 'measured_and_authoritative'],
            ],
            'skeptical_analyst' => [
                'forbidden_hooks' => ['confession'],
                'allowed_hooks'   => ['number_fact', 'quote', 'question'],
                'allowed_tones'   => ['skeptical_and_sharp', 'quietly_intense'],
            ],
            'enthusiastic_rebel' => [
                'forbidden_hooks' => [],
                'allowed_hooks'   => ['confession', 'question', 'number_fact', 'scene_setting'],
                'allowed_tones'   => ['provocative_and_combative', 'urgently_investigative'],
            ],
            'warmer_storyteller' => [
                'forbidden_hooks' => [],
                'allowed_hooks'   => ['confession', 'scene_setting', 'quote'],
                'allowed_tones'   => ['conversational_but_precise', 'reflective_and_layered'],
            ],
            'provocative_essayist' => [
                'forbidden_hooks' => [],
                'allowed_hooks'   => ['confession', 'question', 'number_fact'],
                'allowed_tones'   => ['provocative_and_combative', 'quietly_intense'],
            ],
            'weary_insider' => [
                'forbidden_hooks' => [],
                'allowed_hooks'   => ['confession', 'scene_setting', 'quote'],
                'allowed_tones'   => ['reflective_and_layered', 'measured_and_authoritative'],
            ],
        ];
        $matrix = $compatMatrix[$styleSeeds[0]] ?? [];
        // Resolve hookType conflict
        if (!empty($matrix['forbidden_hooks']) && in_array($hookTypes[0], $matrix['forbidden_hooks'])) {
            $hookTypes[0] = $matrix['allowed_hooks'][array_rand($matrix['allowed_hooks'])];
        }
        // Resolve toneBlend conflict
        if (!empty($matrix['allowed_tones']) && !in_array($toneBlends[0], $matrix['allowed_tones'])) {
            $toneBlends[0] = $matrix['allowed_tones'][array_rand($matrix['allowed_tones'])];
        }

        return [
            'styleSeed'        => $styleSeeds[0],
            'structureVariant' => $structureVariants[0],
            'hookType'         => $hookTypes[0],
            'imagePlacement'   => $imagePlacements[0],
            'openingStrategy'  => $openings[0],
            'analogyDomain'    => $analogyDomains[0],
            'humanNoise'       => $humanNoise[0],
            'toneBlend'        => $toneBlends[0],
            'paragraphRules'   => $paragraphRules,
            'temperature'      => round($temperature, 2),
        ];
    }
}
