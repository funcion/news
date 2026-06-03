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

        // --- FILTER: Article Age ---
        $maxAgeDays = $this->rawArticle->source->max_age_days ?? 7;
        if ($this->rawArticle->published_at && $this->rawArticle->published_at->lt(now()->subDays($maxAgeDays))) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::info("RawArticle {$this->rawArticle->id} rejected: article is {$this->rawArticle->published_at->diffForHumans()}, max age is {$maxAgeDays} days.");
            return;
        }

        // --- FILTER: Source Trust ---
        if (!$this->rawArticle->source->trusted && $this->rawArticle->source->score < 50) {
            $this->rawArticle->update(['status' => 'ignored']);
            Log::warning("RawArticle {$this->rawArticle->id} rejected: untrusted source with low score ({$this->rawArticle->source->score}).");
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
            $categoryId = $this->rawArticle->source->category_id ?? 1; // Use source default but flag it
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

        $sourceTrusted = $this->rawArticle->source->trusted ? 'YES' : 'NO';
        $sourceScore   = $this->rawArticle->source->score ?? 0;
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

        $prompt = <<<PROMPT
You are a {$persona}. Your job: write a compelling, deeply human OPINION COLUMN.

DATE: {$today} | TYPE: {$contentType} | TARGET: {$wordTarget}
SOURCE LANGUAGE: {$sourceLangName} (ISO: {$sourceLang})
VERIFIED FACTS FROM SOURCE: {$topic}

NON-NEGOTIABLE: {$rules}
NON-NEGOTIABLE: You MUST produce the final article in BOTH English AND Spanish.

═══ 1. VOICE & AUTHENTICITY (HIGHEST PRIORITY) ═══

AUTHOR PERSONA:
- Name: {$authorNameEn}
- Bio/Background: {$authorBioEn}

You MUST adopt the professional persona, opinions, and expertise of {$authorNameEn} based on their background. 

DYNAMIC EDITORIAL VOICE (CRITICAL):
Rather than sticking to a single rigid tone of voice, you must dynamically blend multiple tones of voice (e.g., analytical, inquisitive, provocative, informative, skeptical, visionary) to perfectly fit the specific topic and gravity of the news. The writing style must feel like an organic, living human voice that responds organically to the nuances of the events.

TAKE A CLEAR STANCE. Every column needs a thesis: is this overhyped? Dangerous? Quietly brilliant? Reckless? State it by paragraph 2. Commit fully. A columnist who hedges on everything is a columnist nobody reads. When claims from the source are unverified, flag them honestly ("reports suggest", "if confirmed") — but your analytical OPINION about those claims must still be sharp and decisive.

MANDATORY ANCHORS OF REALITY & E-E-A-T (NON-NEGOTIABLE):
- MANDATORY UNCERTAINTY: Include EXACTLY ONE sentence in each language version where the author admits uncertainty, a limitation of their argument, or a confession of doubt. Example: "Look, I could be wrong about the timeline. Maybe the Jevons paradox doesn't apply cleanly to software labor. But..." This humanizes the text (AIs never admit doubt).
- NO EXTERNAL URLS IN CONTENT: NEVER include any URLs, hyperlinks, or external links inside the article body (content_en or content_es). Do NOT reference external studies, surveys, or reports with URLs. Your analysis must rely solely on the verified facts provided and your own expert reasoning. Cite sources by name only (e.g., "a 2024 Stack Overflow survey found..." without a link).

- MANDATORY PERSONAL EXPERIENCE: Write from a first-person perspective ("I", "we") that includes ONE specific personal/professional observation framed as a direct professional reflection (e.g., "In my experience writing about AI tools since 2023...", "When I analyzed similar products last quarter..."). Never fabricate meetings, conversations, or specific incidents.

WRITE WITH GENUINE PERSONALITY:
- Use contractions naturally: "don't", "isn't", "we've", "they're"
- Use em dashes — like this — for dramatic asides or mid-thought clarifications
- Allow yourself strong adjectives: "reckless", "lazy", "alarming", "brilliant" — not safe ones like "interesting" or "notable"
- Vary sentence rhythm organically. A short punch. Then a longer analytical sentence that unpacks the implications with genuine nuance, subordinate clauses, and informed context.
- Vary paragraph lengths naturally: include at least ONE very short paragraph (1 sentence, 5-10 words) for high rhetorical punch, and at least ONE long paragraph (6-8 sentences) in both language contents.
- Occasionally address the reader directly when it serves the argument
- Weave in 1 real-world analogy (historical event, pop culture, everyday life) to explain a complex concept — never invent fictional scenarios
- Challenge PR spin and vague marketing language with specific counter-evidence

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

═══ 3. ARTICLE STRUCTURE ═══

1. HOOK (1-2 sentences): A specific, concrete fact — a number, a launch date, a CEO quote, a technical failure. Not a vague observation.
2. THESIS (paragraph 2): Your clear, opinionated stance on why this fact matters. State it directly.
3. BODY (2-3 sections with H2 headings): Each section advances your argument. You MUST alternate your text blocks with structural image tokens. A section cannot contain more than two consecutive paragraphs without being separated by an `[IMAGE_N]` token on its own standalone line. Use <strong> for key data points, <blockquote> for critical insights or notable quotes, <ul>/<ol> for scannable information.
4. CLOSE (1 paragraph): A prediction, warning, or provocation. End with a sentence the reader would quote to a colleague.

ALLOWED HTML TAGS ONLY: <p>, <h2>, <strong>, <blockquote>, <ul>, <ol>, <li>. NEVER use <h1>, <h3>, <h4>, <div>, <span>, or markdown bold (**) inside the HTML content strings.

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
    "content_en": "<p>Concrete hook grounded in a specific fact from the source.</p>\n<p>Your thesis stated directly and decisively.</p>\n<h2>First Analytical Section</h2>\n<p>Evidence, context, and reasoning that advances the argument...</p>\n<p>Continued analysis with data points and industry comparison...</p>\n[IMAGE_2]\n<p>Deeper exploration after the image, building on the visual context...</p>\n<h2>Second Section with Different Angle</h2>\n<p>Further argument development with counter-evidence or historical parallel...</p>\n[IMAGE_3]\n<p>Analysis connecting this section to the broader thesis...</p>\n<h2>What This Actually Means</h2>\n<p>Implications grounded in evidence, not speculation...</p>\n<p>Closing paragraph with a memorable, quotable final sentence.</p>",
    "content_es": "<p>Gancho concreto basado en un hecho especifico de la fuente.</p>\n<p>Tu tesis expresada de forma directa y decidida.</p>\n<h2>Primera Seccion Analitica</h2>\n<p>Evidencia, contexto y razonamiento que avanza el argumento...</p>\n<p>Analisis continuado con datos y comparacion de industria...</p>\n[IMAGE_2]\n<p>Exploracion mas profunda despues de la imagen...</p>\n<h2>Segunda Seccion con Angulo Diferente</h2>\n<p>Desarrollo del argumento con contra-evidencia o paralelo historico...</p>\n[IMAGE_3]\n<p>Analisis conectando esta seccion con la tesis general...</p>\n<h2>Lo Que Esto Realmente Significa</h2>\n<p>Implicaciones basadas en evidencia, no especulacion...</p>\n<p>Parrafo de cierre con una frase memorable y citable.</p>",
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

FINAL SELF-CHECK (verify ALL before outputting):
1. Every [IMAGE_N] (N>=2) appears on its OWN LINE in BOTH content_en AND content_es
2. [IMAGE_1] does NOT appear anywhere inside content_en or content_es
3. Date used is EXACTLY: {$today}
4. Zero fabricated quotes, conversations, or personal experiences
5. Zero phrases from the BLOCKED PHRASES list above
6. Spanish reads as native writing, not as a translation
7. Title is max 60 chars | Excerpt is max 155 chars | Slug is max 6 words
PROMPT;

        $response = $ai->complete([['role' => 'user', 'content' => $prompt]], OpenRouterService::MODEL_ACTIVE);
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

        // 5. Title length check
        if (mb_strlen($data['title_en'] ?? '') > 80) {
            $errors[] = 'title_en exceeds 80 characters';
        }
        if (mb_strlen($data['title_es'] ?? '') > 80) {
            $errors[] = 'title_es exceeds 80 characters';
        }

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
            'inflection point', 'unprecedented scale', 'seamlessly integrate',
            'robust ecosystem', 'the digital landscape', 'it remains to be seen',
            'only time will tell', 'it\'s worth noting', 'in today\'s rapidly evolving',
            'raises important questions', 'the implications are profound',
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
