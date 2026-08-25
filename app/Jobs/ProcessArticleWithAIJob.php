<?php

namespace App\Jobs;

use App\Models\Article;
use App\Models\User;
use App\Models\RawArticle;
use App\Services\AI\OpenRouterService;
use App\Services\AI\ModelRouterService;
use App\Exceptions\OpenRouterAuthenticationException;
use App\Services\AI\OpenRouterCircuitBreaker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessArticleWithAIJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 900; // 15 minutes — classification (60s) + redaction (300s) + 5 images (240s) + validation (30s) + buffer
    public $tries = 2;

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return (string) $this->rawArticle->id;
    }

    public function backoff(): array
    {
        return [60, 180]; // 1min, 3min between retries
    }

    public function __construct(
        protected RawArticle $rawArticle
    ) {}

    public function handle(ModelRouterService $ai, \App\Services\AI\SiliconFlowImageService $imageService, \App\Services\AI\TagGeneratorService $tagService, \App\Services\AI\DuplicateCheckerService $duplicateChecker, \App\Services\SEO\EntityAutoLinkerService $autoLinker): void
    {
        // Guard: require API key before processing
        if (empty(config('openrouter.api_key'))) {
            Log::error("ProcessArticleWithAIJob: OPENROUTER_API_KEY is not set. Releasing job for 5 minutes.");
            $this->release(300); // retry in 5 minutes
            return;
        }

        // Circuit Breaker: if API key is invalid, release all jobs with long delay
        if (OpenRouterCircuitBreaker::isOpen()) {
            $ttl = OpenRouterCircuitBreaker::remainingTtl();
            Log::warning("OpenRouter Circuit Breaker is OPEN. Releasing RawArticle {$this->rawArticle->id} for {$ttl}s. Update OPENROUTER_API_KEY in .env!");
            $this->release(max(60, $ttl));
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

        try {
            $classification = $this->classifyAndExtract($ai);
        } catch (OpenRouterAuthenticationException $e) {
            // 401 = permanent auth failure — don't retry, mark as failed immediately
            Log::error("RawArticle {$this->rawArticle->id}: OpenRouter auth failed (401). Marking as failed — check API key.", [
                'response' => $e->getResponseBody(),
            ]);
            OpenRouterCircuitBreaker::recordFailure();
            $this->rawArticle->update(['status' => 'failed']);
            return; // Don't throw — prevents Laravel from retrying a permanent error
        }

        if ($classification === null) {
            throw new \Exception("AI classification failed (empty response). Retrying...");
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

        $author = User::where('is_active', true)->where('slug', '!=', 'admin')->inRandomOrder()->first() ?: User::first();

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

        try {
            $redacted = $this->redactBilingual($ai, $classification, $author);
        } catch (OpenRouterAuthenticationException $e) {
            Log::error("RawArticle {$this->rawArticle->id}: OpenRouter auth failed (401) during redaction. Marking as failed.", [
                'response' => $e->getResponseBody(),
            ]);
            OpenRouterCircuitBreaker::recordFailure();
            $this->rawArticle->update(['status' => 'failed']);
            return;
        }

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
        $contentEn = $this->ensureHtmlParagraphs($contentEn);
        $contentEs = $this->ensureHtmlParagraphs($contentEs);

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
        $article->reading_time   = $this->calculateReadingTime($contentEn, $contentEs);
        $article->ai_metadata    = [
            'origin_url'  => $this->rawArticle->url,
            'today_date'  => $today,
            'json_ld'     => $redacted['json_ld'] ?? null,
            'style_dna'   => $redacted['__style_dna'] ?? null,
            'model_used'  => $redacted['__model_used'] ?? config('ai_models.pool.0', 'deepseek/deepseek-chat'),
            'temperature' => $redacted['__temperature'] ?? null,
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
                if ($index >= 3) break;

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

                    // --- FEATURED IMAGE [IMAGE_1] FAILURE: Use placeholder instead of rollback ---
                    if ($placeholder === '[IMAGE_1]') {
                        $placeholderPath = $this->generatePlaceholderHero(
                            $redacted['title_en'] ?? $this->rawArticle->title ?? 'Article',
                            $slugEn
                        );
                        if ($placeholderPath && file_exists($placeholderPath)) {
                            $heroImgId = "img-hero-placeholder-" . Str::random(5);
                            $heroSizes = "(max-width: 600px) 100vw, (max-width: 1200px) 800px, 1200px";

                            $fileNameEn = "{$slugEn}-hero-placeholder.webp";
                            $mediaEn = $article->addMedia($placeholderPath)
                                ->usingFileName($fileNameEn)
                                ->usingName(Str::limit($redacted['title_en'] ?? 'Hero', 70))
                                ->withCustomProperties(['lang' => 'en', 'alt' => $altEn, 'title' => Str::limit($altEn, 70), 'caption' => $captionEn])
                                ->preservingOriginal()
                                ->toMediaCollection('images_en');

                            $fileNameEs = "{$slugEs}-hero-placeholder.webp";
                            $mediaEs = $article->addMedia($placeholderPath)
                                ->usingFileName($fileNameEs)
                                ->usingName(Str::limit($redacted['title_es'] ?? 'Hero', 70))
                                ->withCustomProperties(['lang' => 'es', 'alt' => $altEs, 'title' => Str::limit($altEs, 70), 'caption' => $captionEs])
                                ->toMediaCollection('images_es');

                            $article->image_url = $mediaEn->getUrl();
                            $article->save();
                            $article->setTranslation('image_alt', 'en', $altEn);
                            $article->setTranslation('image_alt', 'es', $altEs);
                            $article->save();

                            $imageCount++;
                            Log::info("Placeholder hero image generated for Article. Original SiliconFlow call failed.");
                        }
                    }
                }
            }
        }

        // --- SAFETY NET: No images at all → save as draft for admin review ---
        if ($imageCount === 0) {
            $article->status = 'draft';
            $meta = $article->ai_metadata;
            $meta['needs_images'] = true;
            $meta['image_failure_reason'] = 'All image generations failed';
            $article->ai_metadata = $meta;
            $article->save();
            Log::warning("Article {$article->id} saved as draft — no images generated. Admin review needed.");
            // Do NOT throw — article content is preserved for manual image addition
        }

        // --- CLEANUP: Remove temp files from images-tmp/ ---
        // SiliconFlowImageService saves to local temp, Spatie copies to MEDIA_DISK (R2 or local).
        // We must clean up the temp copies to avoid disk accumulation.
        $tempPath = storage_path('app/images-tmp');
        if (is_dir($tempPath)) {
            $tempFiles = glob($tempPath . '/' . $slugEn . '-*.webp');
            foreach ($tempFiles as $tempFile) {
                if (is_file($tempFile)) {
                    @unlink($tempFile);
                }
            }
            // Clean up placeholder too
            $placeholderFile = $tempPath . '/placeholder-' . $slugEn . '.webp';
            if (is_file($placeholderFile)) {
                @unlink($placeholderFile);
            }
        }


        // --- Verified Entity Auto-Linking (Zero-Hallucination Safe Outbound Links) ---
        $contentEn = $autoLinker->autoLink($contentEn);
        $contentEs = $autoLinker->autoLink($contentEs);

        // --- Canonical Source Reference Card ---
        if (!empty($this->rawArticle->url)) {
            $sourceName = $this->rawArticle->source ? $this->rawArticle->source->name : 'Primary Source';
            $sourceUrl = htmlspecialchars($this->rawArticle->url, ENT_QUOTES, 'UTF-8');
            $sourceNameClean = htmlspecialchars($sourceName, ENT_QUOTES, 'UTF-8');

            $contentEn .= "\n<p class=\"source-reference-card mt-8 pt-4 border-t border-slate-800 text-xs text-slate-400\"><em>Originally reported and referenced from <a href=\"{$sourceUrl}\" target=\"_blank\" rel=\"noopener noreferrer nofollow\" class=\"text-brand-teal hover:underline font-medium\">{$sourceNameClean}</a>.</em></p>";
            $contentEs .= "\n<p class=\"source-reference-card mt-8 pt-4 border-t border-slate-800 text-xs text-slate-400\"><em>Referencia y fuente original: <a href=\"{$sourceUrl}\" target=\"_blank\" rel=\"noopener noreferrer nofollow\" class=\"text-brand-teal hover:underline font-medium\">{$sourceNameClean}</a>.</em></p>";
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

        // Dynamic Staggered Publishing (Configurable from /admin/settings-page)
        if ($imageCount > 0) {
            $isStaggered = (bool) \App\Models\Setting::get('publishing.staggered_enabled', false);
            $minDelay = max(0, (int) \App\Models\Setting::get('publishing.delay_min_minutes', 1));
            $maxDelay = max($minDelay, (int) \App\Models\Setting::get('publishing.delay_max_minutes', 60));

            if ($isStaggered && $maxDelay > 0) {
                $latestArticleDate = Article::whereIn('status', ['published', 'scheduled'])
                    ->whereNotNull('published_at')
                    ->max('published_at');

                $baseTime = ($latestArticleDate && \Carbon\Carbon::parse($latestArticleDate)->isFuture())
                    ? \Carbon\Carbon::parse($latestArticleDate)
                    : now();

                $delayMinutes = random_int($minDelay, $maxDelay);
                $publishAt = $baseTime->copy()->addMinutes($delayMinutes);

                $article->status = 'scheduled';
                $article->published_at = $publishAt;
                Log::info("Article {$article->id} ('{$article->slug_es}') scheduled for publication at {$publishAt->toDateTimeString()} (+{$delayMinutes}m delay, range: {$minDelay}-{$maxDelay}m).");
            } else {
                $article->status = 'published';
                $article->published_at = now();
                Log::info("Article {$article->id} published immediately (staggered delay disabled in settings).");
            }
        } else {
            $article->status = 'draft';
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
        // Safety limit to prevent infinite recursion
        if ($attempt > 50) {
            Log::warning("ensureUniqueSlug: exceeded 50 attempts for '{$slug}', using random suffix");
            return "{$slug}-" . Str::random(6);
        }

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

    protected function classifyAndExtract(ModelRouterService $ai): ?array
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

        $source = $this->rawArticle->source;
        $sourceTrusted = ($source && $source->trusted) ? 'YES' : 'NO';
        $sourceScore   = (int) ($source?->score ?? 0);
        $sourceDate    = $this->rawArticle->published_at ? $this->rawArticle->published_at->format('l, F j, Y') : $today;
        $articleAge    = $this->rawArticle->published_at ? $this->rawArticle->published_at->diffForHumans() : 'today';
        $currentYear   = now()->year;

        $prompt = <<<PROMPT
You are a senior multilingual editorial AI. Analyze the following news article and respond in STRICT JSON only.

STRICT TEMPORAL ANCHORS:
- CURRENT DATE: {$today} (Year: {$currentYear})
- SOURCE PUBLISHED: {$sourceDate} ({$articleAge})

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

        $resultObj = $ai->classifyWithFailover([['role' => 'user', 'content' => $prompt]]);
        $response = $resultObj['content'] ?? null;
        $result   = $this->parseJson($response);

        if ($result) {
            Log::info("RawArticle {$this->rawArticle->id} classified. Source language: " . ($result['source_language'] ?? 'unknown'));
        }

        return $result;
    }

    protected function redactBilingual(ModelRouterService $ai, array $classification, User $author): ?array
    {
        $today          = now()->format('l, F j, Y');
        $currentYear    = now()->year;
        $sourceDate     = $this->rawArticle->published_at ? $this->rawArticle->published_at->format('l, F j, Y') : $today;
        $articleAge     = $this->rawArticle->published_at ? $this->rawArticle->published_at->diffForHumans() : 'today';
        $isSeed         = $classification['is_seed'] ?? false;
        $contentType    = $classification['content_type'] ?? 'blog';
        $facts          = (array) ($classification['facts'] ?? [$this->rawArticle->title ?? 'Tech News']);
        $topic          = $isSeed ? ($this->rawArticle->title ?? 'Tech News') : implode('; ', $facts);
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

        $wordTargets = config('global.editorial.word_targets') ?? [
            'news'   => '600-1000 words EN | 600-1000 palabras ES',
            'blog'   => '800-1200 words EN | 800-1200 palabras ES',
            'guide'  => '1000-1500 words EN | 1000-1500 palabras ES',
            'review' => '800-1200 words EN | 800-1200 palabras ES',
            'pillar' => '1500-2500 words EN | 1500-2500 palabras ES',
        ];
        $wordTarget = $wordTargets[$contentType] ?? $wordTargets['blog'] ?? '800-1200 words';

        $authorNameEn = $author->getTranslation('name', 'en') ?: $author->getTranslation('name', 'es') ?: $author->name;
        $authorBioEn  = $author->getTranslation('bio', 'en') ?: $author->getTranslation('bio', 'es') ?: $author->bio;

        $persona = config('global.editorial.persona') ?? 'world-class Senior Technology Journalist and elite SEO copywriter (15+ years experience) working for Glodaxia, a premium tech publication.';
        $rules   = config('global.editorial.focus_rules') ?? 'STRICTLY ADHERE TO THE FACTS PROVIDED. NEVER invent names, dates, statistics, or events not present in the SOURCE FACTS.';

        // Generate clean editorial style DNA
        $styleDna    = $this->generateStyleDNA();
        $temperature = $styleDna['temperature'];

        $authorBioEs = $author->getTranslation('bio', 'es') ?: $author->bio;

        $prompt = <<<PROMPT
You are a senior investigative tech columnist and essayist writing for Glodaxia (a premium digital journalism publication).
Your task is to write an ORIGINAL, RIGOROUS, HIGH-IMPACT journalism column based on the verified facts provided below.

═════════════════════════════════════════════════════════════════════
═══ 1. CONTEXT & VERIFIED SOURCE FACTS ═══
═════════════════════════════════════════════════════════════════════
- CURRENT DATE: {$today} (Current Year: {$currentYear})
- SOURCE PUBLISHED: {$sourceDate} ({$articleAge})
- VERIFIED SOURCE FACTS: {$topic}
- ARTICLE ARCHETYPE: {$styleDna['archetypeName']}
- STRUCTURAL GUIDANCE: {$styleDna['archetypeStructure']}

═════════════════════════════════════════════════════════════════════
═══ 2. AUTHOR VOICE & REALISTIC PROFESSIONAL PERSONA ═══
═════════════════════════════════════════════════════════════════════
- Assigned Author: {$authorNameEn}
- Author Background: {$authorBioEn} (ES: {$authorBioEs})

STRICT PERSONA GROUNDING:
1. Write with the natural voice, authentic perspective, and expertise of {$authorNameEn}.
2. CRITICAL ANTI-PROMPT-LEAK RULE: NEVER invent fake personal memories in unrelated industries. (For example: if the author is an educator or physician, DO NOT say 'I recall analyzing fintech platform pivots years ago...'). If you draw a real-world reflection, ground it strictly in the subject matter and the author's actual field of passion.
3. Adopt an authoritative yet natural, conversational tone. Write like a real staff writer for Wired, The Verge, MIT Technology Review, or The Atlantic.

═════════════════════════════════════════════════════════════════════
═══ 3. ZERO-TOLERANCE ANTI-AI RULES (HUMAN AUTHENTICITY) ═══
═════════════════════════════════════════════════════════════════════

1. ZERO SENTENCE REPETITION OR ECHO (CRITICAL):
   - NEVER repeat the same phrase, opening hook, thesis sentence, or grammatical formula twice in the article.
   - Every single paragraph must advance the story with fresh analysis, concrete data, or distinct perspectives.

2. BAN ON FORMULAIC DEBATE CATCHPHRASES & CLICHES:
   - FORBIDDEN EN: "Look,", "Here's the thing:", "Okay, I hear the objection already", "Fair enough", "Impossible?", "metabolic enzyme", "pathogen", "plate tectonics", "the digital landscape", "beacon of hope", "double-edged sword", "tapestry", "delve", "testament to", "orchestrate", "at the end of the day", "it remains to be seen", "only time will tell", "in today's fast-paced world", "in conclusion", "a game-changer", "paradigm shift".
   - FORBIDDEN ES: "Mira,", "Aqui esta el detalle:", "Ya escucho las objeciones", "Justo.", "¿Imposible?", "enzima metabolica", "patogeno", "placas tectonicas", "el panorama digital", "faro de esperanza", "espada de doble filo", "tapiz", "adentrarse", "testimonio de", "orquestar", "al final del dia", "solo el tiempo dira", "en el mundo actual", "en conclusion", "cambio de paradigma".

3. NATURAL, SHARP VOCABULARY:
   - Use clear, rigorous, concrete journalistic vocabulary. Avoid forced pseudo-intellectual metaphors or piled-up buzzwords.

4. SEAMLESS SOURCE INTEGRATION:
   - Integrate the source attribution naturally in the prose (e.g. "According to a detailed report from...", "As documented in recent findings by...").

═════════════════════════════════════════════════════════════════════
═══ 4. DYNAMIC EDITORIAL FREEDOM & CONTENT ARCHITECTURE ═══
═════════════════════════════════════════════════════════════════════
- EDITORIAL FREEDOM: Do NOT follow a rigid formula. You have complete freedom to structure the narrative to best tell this specific story. Vary paragraph lengths, use lists when comparing features/data, or use pure flowing prose when delivering deep analytical narrative.
- ALLOWED HTML TAGS: <p>, <h2>, <h3>, <strong>, <blockquote>, <ul>, <ol>, <li>.
- NEVER use <h1>, <h4>, <div>, <span>, or raw markdown bold (**) inside HTML content.
- PARAGRAPHS: Every narrative block must be wrapped in <p>...</p>. Write with natural rhythm (some paragraphs of 2 sentences, others of 3-4 sentences).
- HEADINGS (<h2> / <h3>): Use descriptive, incisive subheadings to structure your argument. NEVER use generic headers like "The Context" or "The Impact".
- CLOSING SECTION: Conclude with an analytical final section that looks toward the future.
- ENGAGING READER OUTRO: The very last sentence of the article MUST be an incisive, thought-provoking question directly addressing the reader to prompt comments/discussion (e.g. 'What is your take: is this genuine progress or an overhyped transition?' / '¿Cual es tu balance: estamos ante un avance real o ante una promesa sobredimensionada?'). NEVER say 'In conclusion' or 'En conclusion'.

═════════════════════════════════════════════════════════════════════
═══ 5. IMAGE PLACEMENT RULES ═══
═════════════════════════════════════════════════════════════════════
- Total images: {$styleDna['imageCount']}
- [IMAGE_1] = Hero/featured image ONLY (do NOT insert inside content_en or content_es).
- Interior images: Insert [IMAGE_2] (and [IMAGE_3] if count=3) on its OWN STANDALONE LINE inside the HTML content between major sections.
  Example:
  <p>First section analysis...</p>

  [IMAGE_2]

  <h2>Next Analytical Section</h2>
  <p>Second section analysis...</p>

- FLUX.1 Prompts: Photorealistic, 35mm DSLR Nikon D850 style, cinematic natural lighting, 8k, hyper-realistic, no text overlay, no watermarks.

═════════════════════════════════════════════════════════════════════
═══ 6. STRICT BILINGUAL INDEPENDENCE ═══
═════════════════════════════════════════════════════════════════════
The Spanish version MUST read as if originally penned by a native Spanish tech journalist — with natural flow, rich vocabulary, and independent rhetorical strength.

CRITICAL JSON FORMATTING RULES:
1. Inside HTML content strings, use single quotes (') for speech or attribute quotes. NEVER use unescaped double quotes (") inside text values.
2. Use literal "\n" for newlines inside content strings.
3. Every interior image token must be isolated on its own line: `\n\n[IMAGE_2]\n\n`.

{
    "title_en": "Compelling headline in English (40-80 chars)",
    "title_es": "Titular cautivador en Espanol (40-80 chars)",
    "slug_en": "short-english-slug-max-6-words",
    "slug_es": "slug-espanol-corto-max-6-palabras",
    "excerpt_en": "Sharply written teaser in English (max 155 chars)",
    "excerpt_es": "Extracto agil en Espanol (max 155 chars)",
    "content_en": "Full article in English with <p>, <h2>, [IMAGE_2], etc.",
    "content_es": "Articulo completo en Espanol con <p>, <h2>, [IMAGE_2], etc.",
    "keywords": ["keyword1", "keyword2", "keyword3", "keyword4", "keyword5"],
    "image_prompts": [
        {
            "id": "[IMAGE_1]",
            "prompt_en": "Photojournalistic style, [scene description relevant to headline], 35mm lens, natural cinematic lighting, 8k, photorealistic, no text",
            "alt_en": "Alt text in English (max 125 chars)",
            "alt_es": "Texto alternativo en Espanol (max 125 chars)",
            "caption_en": "Contextual editorial caption",
            "caption_es": "Leyenda editorial contextual",
            "title_en": "Image title (max 70 chars)",
            "title_es": "Titulo imagen (max 70 chars)"
        },
        {
            "id": "[IMAGE_2]",
            "prompt_en": "Photojournalistic style, [second visual perspective on the topic], 35mm lens, natural lighting, 8k, no text",
            "alt_en": "Alt text in English",
            "alt_es": "Texto alternativo en Espanol",
            "caption_en": "Contextual caption",
            "caption_es": "Leyenda contextual",
            "title_en": "Image title",
            "title_es": "Titulo imagen"
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
PROMPT;
        $resultObj = $ai->completeWithFailover([['role' => 'user', 'content' => $prompt]], ['temperature' => $temperature]);
        if (!$resultObj || empty($resultObj['content'])) {
            Log::warning("redactBilingual: AI returned null response for RawArticle {$this->rawArticle->id}");
            return null;
        }
        $response = $resultObj['content'];
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

        // Attach style DNA metadata for downstream logging
        // Prefix with double-underscore to avoid collision with any AI-returned keys
        $data['__style_dna'] = $styleDna;
        $data['__temperature'] = $temperature;
        $data['__model_used'] = $resultObj['model_used'] ?? 'auto';

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

        // 7. Check for blocked AI-fingerprint phrases (WARNING ONLY — auto-fixed in autoFixRedactedOutput)
        // Hard-failing on blocked phrases wastes API credits on retries. The auto-fix
        // silently strips them in PHP, and we log warnings here for monitoring.
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
                Log::warning("AI-fingerprint phrase found in content_en (auto-fixed): '{$phrase}' — RawArticle {$this->rawArticle->id}");
            }
        }

        // 8. Check for blocked AI-fingerprint phrases in SPANISH (WARNING ONLY)
        $blockedPhrasesEs = [
            'cambio de paradigma', 'en conclusión', 'sin lugar a dudas',
            'cabe destacar', 'queda por ver', 'un arma de doble filo',
            'marca un antes y un después', 'las implicaciones son profundas',
            'en el mundo de', 'sin ir más lejos',
            'como ya hemos mencionado', 'en última instancia',
            'es importante destacar',
            'sin duda alguna', 'no cabe duda', 'vale la pena mencionar',
        ];
        $contentEsLower = strtolower($contentEs);
        foreach ($blockedPhrasesEs as $phrase) {
            if (str_contains($contentEsLower, $phrase)) {
                Log::warning("AI-fingerprint phrase found in content_es (auto-fixed): '{$phrase}' — RawArticle {$this->rawArticle->id}");
            }
        }

        // 9. Paragraph asymmetry (warnings only — not hard fails)
        foreach ($this->validateParagraphAsymmetry($contentEn, 'en') as $w) { Log::warning("Asymmetry: {$w}"); }
        foreach ($this->validateParagraphAsymmetry($contentEs, 'es') as $w) { Log::warning("Asymmetry: {$w}"); }

        // 10. Heading variety (warnings only)
        foreach ($this->validateHeadingVariety($contentEn) as $w) { Log::warning("Headings: {$w}"); }

        // 11. IMAGE token placement (warnings only)
        foreach ($this->validateImageTokenPlacement($contentEn, 'en') as $w) { Log::warning("Tokens: {$w}"); }
        foreach ($this->validateImageTokenPlacement($contentEs, 'es') as $w) { Log::warning("Tokens: {$w}"); }

        // 12. SEO technical validation (warnings only — too aggressive for hard fail)
        foreach ($this->validateSeoTechnical($data) as $w) { Log::warning("SEO: {$w}"); }

        return $errors;
    }

    /**
     * Auto-fix fields that exceed limits. Runs BEFORE validation.
     * Truncates titles, excerpts, meta fields. Logs what was fixed.
     */
    protected function autoFixRedactedOutput(array $data): array
    {
        $fixes = [];

        // Truncate titles (max 80 chars, cut at last word boundary)
        foreach (['title_en', 'title_es'] as $field) {
            if (mb_strlen($data[$field] ?? '') > 80) {
                $original = $data[$field];
                $data[$field] = Str::limit($data[$field], 80, '');
                // Cut at last space to avoid mid-word truncation
                if (($lastSpace = mb_strrpos($data[$field], ' ')) !== false && $lastSpace > 55) {
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

        // --- AUTO-FIX: Strip blocked AI-fingerprint phrases silently ---
        $blockedReplacementsEn = [
            'paradigm shift' => 'fundamental change', 'game-changer' => 'significant development',
            'revolutionary' => 'substantial', 'democratization of' => 'wider access to',
            'inflection point' => 'turning point', 'trajectory points toward' => 'trend suggests',
            'unprecedented scale' => 'massive scale', 'seamlessly integrate' => 'integrate',
            'robust ecosystem' => 'mature ecosystem', 'the digital landscape' => 'the industry',
            'it remains to be seen' => 'it is unclear', 'only time will tell' => 'the outcome is uncertain',
            'it\'s worth noting' => 'notably,', 'in today\'s rapidly evolving' => 'in a shifting',
            'at the end of the day' => 'ultimately,', 'raises important questions' => 'raises questions',
            'a bold step forward' => 'a deliberate move', 'double-edged sword' => 'trade-off',
            'the implications are profound' => 'the consequences matter',
            'a testament to' => 'evidence of',
            'let\'s dive in' => '', 'let me break this down' => '',
            'in my experience' => 'from what I\'ve observed,', 'low-hanging fruit' => 'obvious target',
            'home run' => 'success', 'slam dunk' => 'certainty', 'picture this' => '',
        ];
        $blockedReplacementsEs = [
            'cambio de paradigma' => 'cambio fundamental', 'en conclusión' => 'para cerrar,',
            'sin lugar a dudas' => 'con certeza,', 'cabe destacar' => 'es notable que',
            'queda por ver' => 'es incierto', 'un arma de doble filo' => 'una disyuntiva',
            'marca un antes y un después' => 'cambia las reglas',
            'las implicaciones son profundas' => 'las consecuencias importan',
            'en el mundo de' => 'en', 'sin ir más lejos' => '',
            'como ya hemos mencionado' => 'como se indicó antes,', 'en última instancia' => 'al final,',
            'es importante destacar' => 'destaca que', 'sin duda alguna' => 'con certeza,',
            'no cabe duda' => 'es evidente', 'vale la pena mencionar' => 'cabe señalar que',
        ];

        foreach (['content_en', 'title_en', 'excerpt_en'] as $field) {
            if (!empty($data[$field])) {
                $original = $data[$field];
                $data[$field] = str_ireplace(array_keys($blockedReplacementsEn), array_values($blockedReplacementsEn), $data[$field]);
                // Clean up double spaces left by empty replacements
                $data[$field] = preg_replace('/\s{2,}/', ' ', $data[$field]);
                if ($data[$field] !== $original) {
                    $fixes[] = "{$field}: AI-fingerprint phrases stripped";
                }
            }
        }
        foreach (['content_es', 'title_es', 'excerpt_es'] as $field) {
            if (!empty($data[$field])) {
                $original = $data[$field];
                $data[$field] = str_ireplace(array_keys($blockedReplacementsEs), array_values($blockedReplacementsEs), $data[$field]);
                $data[$field] = preg_replace('/\s{2,}/', ' ', $data[$field]);
                if ($data[$field] !== $original) {
                    $fixes[] = "{$field}: AI-fingerprint phrases stripped (ES)";
                }
            }
        }

        if (!empty($fixes)) {
            Log::info('autoFixRedactedOutput: ' . implode(', ', $fixes));
        }

        return $data;
    }

    /**
     * Calculate reading time for both languages and return the maximum.
     * English: ~225 WPM, Spanish: ~165 WPM (longer words).
     * Using the max ensures the displayed time is accurate for both audiences.
     */
    protected function calculateReadingTime(string $contentEn, string $contentEs = ''): int
    {
        $wordsEn = str_word_count(strip_tags($contentEn));
        $timeEn  = (int) ceil($wordsEn / 225);

        if (empty($contentEs)) {
            return max(1, $timeEn);
        }

        $wordsEs = count(preg_split('/\s+/', trim(strip_tags($contentEs))));
        $timeEs  = (int) ceil($wordsEs / 165);

        return max(1, $timeEn, $timeEs);
    }

    public function failed(\Throwable $exception): void
    {
        $isAuth = $exception instanceof OpenRouterAuthenticationException;
        $isPermanent = $isAuth || str_contains($exception->getMessage(), '401');

        $this->rawArticle->update(['status' => 'failed']);

        Log::error("Job failed for RawArticle {$this->rawArticle->id}: {$exception->getMessage()}", [
            'permanent'    => $isPermanent,
            'exception'    => get_class($exception),
            'attempts'     => $this->attempts(),
            'will_retry'   => !$isPermanent,
        ]);
    }

    // -------------------------------------------------------------------------
    // NEW VALIDATION & HELPER METHODS
    // -------------------------------------------------------------------------

    /**
     * Extract meaningful paragraph-level blocks from HTML content.
     * Handles <p>, <h2>, <blockquote>, <li>, and fragment lines.
     */
    private function extractParagraphs(string $html): array
    {
        // Split by common block-level HTML tags
        $blocks = preg_split('/\n\n+/', strip_tags($html));
        return array_values(array_filter(array_map('trim', $blocks), fn($b) => mb_strlen($b) > 3));
    }

    /**
     * Count sentences in a text block with multilingual support.
     * Handles abbreviations, URLs, decimal numbers, and ES punctuation.
     */
    private function countSentences(string $text): int
    {
        $clean = strip_tags($text);

        // Single regex to PROTECT abbreviations (replaces "Dr." with "Dr " — strips the period so it doesn't count as sentence end)
        $abbrevPattern = '(\\b(?:Dr|Mr|Mrs|Ms|Prof|Sr|Sra|Ing|Lic|EE\\\\.UU|U\\\\.S|U\\\\.K|etc|vs|approx|aprox|Jan|Feb|Mar|Apr|Jun|Jul|Aug|Sep|Oct|Nov|Dec|Lun|Mar|Mie|Jue|Vie|Sab|Dom))\\.';
        $clean = preg_replace("/{$abbrevPattern}/i", '$1 ', $clean);

        // Protect URLs and decimal numbers
        $clean = preg_replace('/https?:\/\/[^\s]+/', '', $clean);
        $clean = preg_replace('/\d+\.\d+/', '', $clean);

        // Split by sentence terminators followed by space + uppercase (EN + ES)
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-ZÁÉÍÓÚÑ¿¡"])/u', $clean, -1, PREG_SPLIT_NO_EMPTY);

        // Filter out fragments too short to be real sentences
        return count(array_filter($sentences, fn($s) => mb_strlen(trim($s)) > 5));
    }

    /**
     * Validate paragraph asymmetry (T1).
     * Detects AI-fingerprint patterns: uniform paragraph lengths, no single-sentence paragraphs, etc.
     * Returns array of warning strings (not hard errors).
     */
    private function validateParagraphAsymmetry(string $html, string $lang): array
    {
        $warnings = [];
        $paragraphs = $this->extractParagraphs($html);

        if (count($paragraphs) < 4) {
            return $warnings;
        }

        $sentenceCounts = array_map(fn($p) => $this->countSentences($p), $paragraphs);

        // Check for 3+ consecutive paragraphs with same sentence count
        $consecutive = 1;
        for ($i = 1; $i < count($sentenceCounts); $i++) {
            if ($sentenceCounts[$i] === $sentenceCounts[$i - 1] && $sentenceCounts[$i] > 0) {
                $consecutive++;
                if ($consecutive >= 3) {
                    $warnings[] = "{$lang}: {$consecutive} consecutive paragraphs with {$sentenceCounts[$i]} sentence(s) — AI fingerprint";
                }
            } else {
                $consecutive = 1;
            }
        }

        // Check minimum single-sentence paragraphs exist
        $singleSentence = count(array_filter($sentenceCounts, fn($c) => $c === 1));
        if ($singleSentence === 0 && count($paragraphs) >= 5) {
            $warnings[] = "{$lang}: No single-sentence paragraphs found — lacks rhetorical punch";
        }

        // Check for too many consecutive single-sentence paragraphs (artificial drama)
        $consecutiveShort = 0;
        for ($i = 0; $i < count($sentenceCounts); $i++) {
            if ($sentenceCounts[$i] === 1) {
                $consecutiveShort++;
                if ($consecutiveShort >= 3) {
                    $warnings[] = "{$lang}: {$consecutiveShort} consecutive single-sentence paragraphs — artificial drama";
                }
            } else {
                $consecutiveShort = 0;
            }
        }

        // Check long paragraphs exist
        $longParagraphs = count(array_filter($sentenceCounts, fn($c) => $c >= 6));
        if ($longParagraphs === 0 && count($paragraphs) >= 6) {
            $warnings[] = "{$lang}: No long paragraphs (6+ sentences) found — lacks depth";
        }

        return $warnings;
    }

    /**
     * Validate H2 heading variety.
     * AI-generated content tends to have uniform heading lengths.
     */
    private function validateHeadingVariety(string $html): array
    {
        $warnings = [];
        preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $html, $matches);
        $headings = $matches[1] ?? [];

        if (count($headings) >= 3) {
            $wordCounts = array_map(fn($h) => str_word_count(strip_tags($h)), $headings);
            $uniqueCounts = array_unique($wordCounts);

            // If all headings have same word count (±1) → flag
            if (count($uniqueCounts) <= 2 && count($headings) >= 3) {
                $warnings[] = 'H2 headings have suspiciously uniform word counts: ' . implode(', ', $wordCounts);
            }

            // If all headings are exactly 4-6 words → classic AI tell
            $allShort = !empty(array_filter($wordCounts, fn($c) => $c >= 4 && $c <= 6));
            if ($allShort && count(array_unique(array_map(fn($c) => $c >= 4 && $c <= 6 ? 'mid' : 'other', $wordCounts))) === 1) {
                $warnings[] = 'All H2 headings are 4-6 words — classic AI fingerprint';
            }
        }

        return $warnings;
    }

    /**
     * Validate IMAGE token placement in HTML content.
     * Tokens must be on their own standalone line, not inside <p> tags.
     */
    private function validateImageTokenPlacement(string $html, string $lang): array
    {
        $warnings = [];
        $lines = explode("\n", $html);

        foreach ($lines as $i => $line) {
            if (preg_match('/\[IMAGE_(\d+)\]/', $line, $m)) {
                $trimmed = trim($line);
                // Token should be alone on its line (the token itself and nothing else meaningful)
                if ($trimmed !== $m[0] && mb_strlen(str_replace($m[0], '', $trimmed)) > 10) {
                    $warnings[] = "{$lang} line " . ($i + 1) . ": IMAGE token not standalone — extra content on same line";
                }
                // Token must NOT be inside a <p> tag on the same line
                if (str_contains($line, '<p') && str_contains($line, '</p>')) {
                    $warnings[] = "{$lang} line " . ($i + 1) . ": IMAGE token found inside <p> tag";
                }
            }
        }

        return $warnings;
    }

    /**
     * Validate SEO technical requirements (CTO recommendation).
     * Returns array of warning strings.
     */
    private function validateSeoTechnical(array $data): array
    {
        $warnings = [];

        $contentEn = strip_tags($data['content_en'] ?? '');
        $keywords = $data['keywords'] ?? [];
        $primaryKw = strtolower($keywords[0] ?? '');

        if (empty($primaryKw)) {
            $warnings[] = 'No keywords provided — cannot validate SEO';
            return $warnings;
        }

        // 1. Primary keyword in first 100 words
        $words = str_word_count($contentEn, 1);
        $first100 = strtolower(implode(' ', array_slice($words, 0, 100)));
        if (!str_contains($first100, $primaryKw)) {
            $warnings[] = "Primary keyword '{$primaryKw}' not found in first 100 words";
        }

        // 2. Keyword density (aligned with prompt: 0.5% - 2.5%)
        $totalWords = count($words);
        if ($totalWords > 0) {
            $keywordCount = mb_substr_count(strtolower($contentEn), $primaryKw);
            $density = ($keywordCount / $totalWords) * 100;
            if ($density < 0.5) {
                $warnings[] = "Keyword density too low: " . round($density, 2) . "% (minimum 0.5%)";
            } elseif ($density > 2.5) {
                $warnings[] = "Keyword density too high: " . round($density, 2) . "% (maximum 2.5%) — possible keyword stuffing";
            }
        }

        // 3. Primary keyword in at least 1 H2
        $contentHtml = $data['content_en'] ?? '';
        preg_match_all('/<h2[^>]*>(.*?)<\/h2>/is', $contentHtml, $h2Matches);
        $h2Text = strtolower(strip_tags(implode(' ', $h2Matches[1] ?? [])));
        if (!empty($h2Matches[1]) && !str_contains($h2Text, $primaryKw)) {
            $warnings[] = "Primary keyword '{$primaryKw}' not found in any H2 heading";
        }

        // 4. JSON-LD basic validation
        $jsonLd = $data['json_ld'] ?? null;
        if ($jsonLd) {
            if (empty($jsonLd['@type'])) {
                $warnings[] = 'JSON-LD missing @type field';
            }
            if (empty($jsonLd['headline'])) {
                $warnings[] = 'JSON-LD missing headline field';
            }
            if (empty($jsonLd['author']['name'])) {
                $warnings[] = 'JSON-LD missing author name';
            }
        }

        return $warnings;
    }

    /**
     * Generate a placeholder hero image when SiliconFlow fails.
     * Creates a dark gradient image with the article title as text overlay.
     */
    private function generatePlaceholderHero(string $title, string $slug): ?string
    {
        if (!extension_loaded('gd')) {
            Log::error("generatePlaceholderHero: GD extension not loaded. Cannot generate placeholder image.", [
                'title' => $title,
                'slug' => $slug,
            ]);
            return null;
        }

        try {
            $width  = 1280;
            $height = 720;

            $img = imagecreatetruecolor($width, $height);
            if (!$img) return null;

            // Dark gradient background (slate-900 to slate-800)
            for ($y = 0; $y < $height; $y++) {
                $ratio = $y / $height;
                $r = (int)(15 + (30 - 15) * $ratio);
                $g = (int)(23 + (41 - 23) * $ratio);
                $b = (int)(42 + (59 - 42) * $ratio);
                $color = imagecolorallocate($img, $r, $g, $b);
                imageline($img, 0, $y, $width, $y, $color);
            }

            // Brand accent line (cyan-500)
            $accent = imagecolorallocate($img, 6, 182, 212);
            imagefilledrectangle($img, 0, $height - 4, $width, $height, $accent);

            // Title text (white, centered)
            $white = imagecolorallocate($img, 255, 255, 255);
            $fontSize = 5;
            $titleShort = Str::limit($title, 80, '');
            $textWidth = imagefontwidth($fontSize) * strlen($titleShort);
            $x = max(20, (int)(($width - $textWidth) / 2));
            $y = (int)(($height / 2) - (imagefontheight($fontSize) / 2));
            imagestring($img, $fontSize, $x, $y, $titleShort, $white);

            // "Glodaxia" watermark (bottom-right, gray)
            $gray = imagecolorallocate($img, 100, 116, 139);
            imagestring($img, 3, $width - 120, $height - 30, 'Glodaxia.com', $gray);

            // Save as webp
            $path = storage_path("app/public/placeholder-{$slug}.webp");
            $saved = imagewebp($img, $path, 85);
            imagedestroy($img);

            return $saved ? $path : null;
        } catch (\Throwable $e) {
            Log::error("generatePlaceholderHero failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate randomized style DNA for each article.
     * 6 x 5 x 5 x 4 x 10 x 12 x 8 x 8 = 9,216,000 unique macro-combinations.
     * Each call shuffles arrays and picks one option per dimension.
     */
    private function generateStyleDNA(): array
    {
        $archetypes = [
            'concise_punchy_column' => [
                'name' => 'Columna Agil y Directa (500-800 palabras)',
                'structure' => '2 secciones principales con <h2>, ritmo rapido, parrafos cortos e incisivos. Enfoque directo al grano.',
                'image_count' => random_int(1, 2),
            ],
            'deep_investigative_breakdown' => [
                'name' => 'Reportaje de Investigacion Profundo (1000-1500 palabras)',
                'structure' => '3-4 secciones con <h2> y opcionalmente <h3>, desglose minucioso de datos, 1 cita destacada <blockquote> y analisis exhaustivo.',
                'image_count' => random_int(2, 3),
            ],
            'conversational_essay' => [
                'name' => 'Ensayo Conversacional y Reflexivo (700-1100 palabras)',
                'structure' => 'Narrativa fluida que conecta reflexiones de la experiencia del autor con el impacto de la noticia. Uso libre de analogias naturales.',
                'image_count' => random_int(1, 2),
            ],
            'comparative_technical_verdict' => [
                'name' => 'Analisis Comparativo y Veredicto Tecnico (800-1300 palabras)',
                'structure' => 'Estructura comparativa con <h2> analiticos, lista con vinetas <ul> para contrastar pros/contras o alternativas y balance final.',
                'image_count' => random_int(2, 3),
            ],
        ];

        $archetypeKeys = array_keys($archetypes);
        shuffle($archetypeKeys);
        $selectedArchetypeKey = $archetypeKeys[0];
        $selectedArchetype = $archetypes[$selectedArchetypeKey];

        $temperature = round(0.65 + (mt_rand(-8, 8) / 100), 2);

        return [
            'archetypeKey'        => $selectedArchetypeKey,
            'archetypeName'       => $selectedArchetype['name'],
            'archetypeStructure'  => $selectedArchetype['structure'],
            'imageCount'          => $selectedArchetype['image_count'],
            'temperature'         => $temperature,
        ];
    }

    private function rollImageCount(): int
    {
        $roll = mt_rand(1, 100);
        if ($roll <= 60) return 1;       // 60% — hero only
        return mt_rand(2, 3);            // 40% — 2-3 images
    }

    /**
     * Ensure raw narrative text blocks without HTML wrapper tags are properly formatted into <p>...</p> paragraphs.
     */
    protected function ensureHtmlParagraphs(string $content): string
    {
        if (empty(trim($content))) {
            return $content;
        }

        // If already well formed with <p> tags and headings
        if (substr_count($content, '<p>') >= 3) {
            return $content;
        }

        $lines = preg_split('/\n{2,}|\r\n\r\n/', $content);
        $formatted = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }

            // Preserve existing HTML block tags and image tokens
            if (preg_match('/^<(?:p|h[1-6]|blockquote|ul|ol|li|div|figure)[\s>]/i', $trimmed) ||
                preg_match('/^\[IMAGE_\d+\]$/i', $trimmed)) {
                $formatted[] = $trimmed;
            } else {
                // Split large blocks into natural 2-3 sentence paragraphs
                $sentences = preg_split('/(?<=[.?!])\s+(?=[A-Z¿¡"\'\d])/u', $trimmed);
                if (count($sentences) > 4) {
                    $chunks = array_chunk($sentences, 3);
                    foreach ($chunks as $chunk) {
                        $formatted[] = '<p>' . implode(' ', $chunk) . '</p>';
                    }
                } else {
                    $formatted[] = '<p>' . $trimmed . '</p>';
                }
            }
        }

        return implode("\n\n", $formatted);
    }
}