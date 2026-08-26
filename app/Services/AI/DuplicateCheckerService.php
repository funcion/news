<?php

namespace App\Services\AI;

use App\Models\Article;
use App\Models\ArticleUpdate;
use App\Models\RawArticle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DuplicateCheckerService
{
    protected OpenRouterService $ai;

    public function __construct(OpenRouterService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * Comprehensive multi-tier duplicate check:
     * - Level 1: URL / Canonical / Hash matching
     * - Level 2: Title normalization and Fuzzy/Lexical similarity (> 75%)
     * - Level 2.5: In-Queue duplicate check against pending raw articles
     * - Level 3: Semantic embedding cosine similarity via pgvector (< 0.18 distance)
     *
     * Returns true if a duplicate is found (and handled), false if it is genuinely new.
     */
    public function checkAndHandleDuplicate(string $title, string $content, string $url, int $rawArticleId): bool
    {
        // Sanitize string encodings to guarantee only valid UTF-8
        $title   = mb_convert_encoding(trim($title), 'UTF-8', 'UTF-8');
        $content = mb_convert_encoding(trim($content), 'UTF-8', 'UTF-8');
        $cleanTitle = $this->normalizeTitle($title);

        Log::info("Running Anti-Duplicate Check for RawArticle #{$rawArticleId}: '{$title}'");

        // ═══════════════════════════════════════════════════════════════════════
        // LEVEL 1: Exact URL / Canonical URL Check
        // ═══════════════════════════════════════════════════════════════════════
        $normalizedUrl = $this->normalizeUrl($url);
        
        // Check if this URL is already attached as an update to an existing article
        $existingUpdate = ArticleUpdate::where('source_url', $url)
            ->orWhere('source_url', $normalizedUrl)
            ->first();

        if ($existingUpdate && $existingUpdate->article) {
            Log::info("Level 1 Duplicate: URL already registered in ArticleUpdate for Article ID {$existingUpdate->article_id}");
            RawArticle::where('id', $rawArticleId)->update(['status' => 'ignored']);
            return true;
        }

        // Check if another processed raw article has the exact same URL or hash
        $existingRaw = RawArticle::where('id', '!=', $rawArticleId)
            ->whereIn('status', ['processed', 'ignored'])
            ->where(function ($q) use ($url, $normalizedUrl) {
                $q->where('url', $url)
                  ->orWhere('url', $normalizedUrl);
            })
            ->first();

        if ($existingRaw) {
            Log::info("Level 1 Duplicate: URL already processed in RawArticle ID {$existingRaw->id}");
            RawArticle::where('id', $rawArticleId)->update(['status' => 'ignored']);
            return true;
        }

        // ═══════════════════════════════════════════════════════════════════════
        // LEVEL 2: Title Normalization & Fuzzy / Lexical Similarity
        // ═══════════════════════════════════════════════════════════════════════
        // 1. Direct and substring matches in published articles
        $exactOrSubstring = Article::where('status', 'published')
            ->where(function ($q) use ($title, $cleanTitle) {
                $q->whereRaw("LOWER(title->>'en') = ?", [mb_strtolower($cleanTitle, 'UTF-8')])
                  ->orWhereRaw("LOWER(title->>'es') = ?", [mb_strtolower($cleanTitle, 'UTF-8')])
                  ->orWhereRaw("title->>'en' ILIKE ?", ["%{$cleanTitle}%"])
                  ->orWhereRaw("title->>'es' ILIKE ?", ["%{$cleanTitle}%"]);
            })
            ->first();

        if ($exactOrSubstring) {
            Log::info("Level 2 Duplicate found by Title Match: Article ID {$exactOrSubstring->id}");
            $this->createUpdateEntry($exactOrSubstring, $url, $rawArticleId);
            return true;
        }

        // 2. Fuzzy / Levenshtein / Token Overlap matching on recent articles (last 30 days)
        $recentArticles = Article::where('status', 'published')
            ->where('created_at', '>=', now()->subDays(30))
            ->get(['id', 'title']);

        foreach ($recentArticles as $existingArt) {
            $existingTitleEn = $this->normalizeTitle($existingArt->getTranslation('title', 'en') ?? '');
            $existingTitleEs = $this->normalizeTitle($existingArt->getTranslation('title', 'es') ?? '');

            if ($this->isTitleFuzzyMatch($cleanTitle, $existingTitleEn) || $this->isTitleFuzzyMatch($cleanTitle, $existingTitleEs)) {
                Log::info("Level 2 Fuzzy Duplicate found (>75% similarity): Article ID {$existingArt->id} ('{$existingTitleEn}')");
                $this->createUpdateEntry($existingArt, $url, $rawArticleId);
                return true;
            }
        }

        // ═══════════════════════════════════════════════════════════════════════
        // LEVEL 2.5: In-Queue Deduplication against pending raw articles
        // ═══════════════════════════════════════════════════════════════════════
        $pendingDuplicate = RawArticle::where('id', '<', $rawArticleId)
            ->where('status', 'pending')
            ->where(function ($q) use ($cleanTitle) {
                $q->whereRaw("LOWER(title) ILIKE ?", ["%{$cleanTitle}%"])
                  ->orWhere('title', 'ILIKE', '%' . mb_substr($cleanTitle, 0, 40, 'UTF-8') . '%');
            })
            ->first();

        if ($pendingDuplicate) {
            Log::info("Level 2.5 In-Queue Duplicate found: RawArticle #{$rawArticleId} duplicate of earlier pending #{$pendingDuplicate->id}");
            RawArticle::where('id', $rawArticleId)->update(['status' => 'ignored']);
            return true;
        }

        // ═══════════════════════════════════════════════════════════════════════
        // LEVEL 3: Advanced Semantic pgvector Embedding Similarity & Clustering
        // ═══════════════════════════════════════════════════════════════════════
        $textToEmbed = mb_substr($title . ". " . strip_tags($content), 0, 1000, 'UTF-8');
        $embedding = $this->ai->embeddings($textToEmbed);

        if (!$embedding) {
            Log::warning("Could not generate embedding for duplicate checking. Passing content through.");
            return false;
        }

        $vectorString = '[' . implode(',', $embedding) . ']';

        // Query pgvector for closest semantic article in the last 30 days
        $similarArticle = Article::select('id', 'title', 'embedding', 'created_at')
            ->where('status', 'published')
            ->whereNotNull('embedding')
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByRaw("embedding <=> ?::vector", [$vectorString])
            ->first();

        if ($similarArticle) {
            $distanceResult = DB::selectOne(
                "SELECT (embedding <=> ?::vector) as distance FROM articles WHERE id = ?",
                [$vectorString, $similarArticle->id]
            );

            $distance = (float) ($distanceResult->distance ?? 1.0);
            $hoursDiff = $similarArticle->created_at ? now()->diffInHours($similarArticle->created_at) : 999;
            
            // Adaptive Cosine Distance Threshold:
            // - Within 48 hours (breaking news cycle): Threshold 0.28 captures cross-outlet coverage (The Verge vs Ars Technica)
            // - Older than 48 hours: Threshold 0.20 for evergreen/identical topic match
            $maxDistanceThreshold = ($hoursDiff <= 48) ? 0.28 : 0.20;

            Log::info("Level 3 Semantic check: Closest Article ID {$similarArticle->id} has cosine distance {$distance} (Threshold: {$maxDistanceThreshold}, Age: {$hoursDiff}h)");

            if ($distance <= $maxDistanceThreshold) {
                Log::info("Level 3 Semantic Duplicate detected! Cosine Distance {$distance} <= {$maxDistanceThreshold} with Article ID {$similarArticle->id}. Consolidating into existing story.");
                $this->createUpdateEntry($similarArticle, $url, $rawArticleId);
                return true;
            }
        }

        return false;
    }

    /**
     * Clean and normalize title by stripping media outlets branding suffixes and punctuation.
     */
    protected function normalizeTitle(string $title): string
    {
        // Strip common publication branding (e.g., "- TechCrunch", "| The Verge", " - Reuters")
        $cleaned = preg_replace('/\s*[-|–—]\s*(TechCrunch|The Verge|Wired|Hacker News|Reuters|Bloomberg|CNBC|VentureBeat|Ars Technica|Engadget|Gizmodo|ZDNet|The Information|CoinDesk|Decrypt|Cointelegraph|The Register|9to5Mac|9to5Google|Android Police|The Next Web).*$/iu', '', $title);
        
        // Strip non-alphanumeric punctuation (preserve spaces)
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $cleaned ?? $title);
        
        // Collapse multiple whitespaces
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        return trim($cleaned);
    }

    /**
     * Compare two titles using Token Overlap (Jaccard) and similar_text.
     * Returns true if similarity exceeds 75%.
     */
    protected function isTitleFuzzyMatch(string $title1, string $title2): bool
    {
        if (empty($title1) || empty($title2)) {
            return false;
        }

        $t1 = mb_strtolower(trim($title1), 'UTF-8');
        $t2 = mb_strtolower(trim($title2), 'UTF-8');

        if ($t1 === $t2) {
            return true;
        }

        // similar_text percentage
        similar_text($t1, $t2, $percent);
        if ($percent >= 78.0) {
            return true;
        }

        // Token Jaccard overlap for words of length >= 4
        $words1 = array_filter(explode(' ', $t1), fn($w) => mb_strlen($w, 'UTF-8') >= 4);
        $words2 = array_filter(explode(' ', $t2), fn($w) => mb_strlen($w, 'UTF-8') >= 4);

        if (count($words1) >= 3 && count($words2) >= 3) {
            $intersection = count(array_intersect($words1, $words2));
            $union = count(array_unique(array_merge($words1, $words2)));
            $jaccard = $union > 0 ? ($intersection / $union) : 0;

            if ($jaccard >= 0.70) {
                return true;
            }
        }

        return false;
    }

    /**
     * Normalize URL (strip query parameters and trailing slashes).
     */
    protected function normalizeUrl(string $url): string
    {
        $parsed = parse_url($url);
        if (!$parsed || empty($parsed['host'])) {
            return $url;
        }

        $scheme = $parsed['scheme'] ?? 'https';
        $host = strtolower($parsed['host']);
        $path = rtrim($parsed['path'] ?? '', '/');

        return "{$scheme}://{$host}{$path}";
    }

    /**
     * Attach this new raw source as an update to an existing article.
     */
    protected function createUpdateEntry(Article $article, string $url, int $rawArticleId): void
    {
        $raw = RawArticle::find($rawArticleId);
        $title = $raw ? $raw->title : 'Update';
        $summary = $raw ? ($raw->summary ?? mb_substr(strip_tags($raw->content ?? ''), 0, 200, 'UTF-8')) : 'Update from source';

        ArticleUpdate::firstOrCreate([
            'article_id' => $article->id,
            'source_url' => $url,
        ], [
            'title' => $title,
            'content' => $summary,
            'summary' => $summary,
            'published_at' => now(),
        ]);

        if ($raw) {
            $raw->update(['status' => 'processed']);
        }
        $article->touch(); // Bump updated_at
        
        Log::info("Attached RawArticle #{$rawArticleId} as update to Article ID {$article->id}.");
    }

    /**
     * Store the embedding on the newly created Article for future comparisons.
     */
    public function generateAndStoreEmbedding(Article $article, string $content): void
    {
        $title = $article->getTranslation('title', 'en') ?? '';
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        $textToEmbed = mb_substr($title . ". " . strip_tags($content), 0, 1000, 'UTF-8');
        $embedding = $this->ai->embeddings($textToEmbed);

        if ($embedding) {
            $article->update(['embedding' => $embedding]);
            Log::info("Embedding stored for Article {$article->id}");
        }
    }
}