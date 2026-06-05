<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishRateLimitedDrafts extends Command
{
    protected $signature = 'articles:publish-pending';
    protected $description = 'Publish draft articles that were held back by rate limits. Run via scheduler.';

    public function handle(): int
    {
        // 1. PRIORITIZE AND PUBLISH ALL "BREAKING NEWS" / SOFTWARE RELEASES IMMEDIATELY (Bypassing Limits)
        // This logic is permanent: Critical news (importance >= 9) or software releases (atom feeds) bypass rate limits
        $breakingDrafts = Article::where('status', 'draft')
            ->where(function ($q) {
                $q->whereNull('ai_metadata->needs_images')
                  ->orWhere('ai_metadata->needs_images', false);
            })
            ->where(function ($q) {
                $q->where('ai_metadata->importance', '>=', 9)
                  ->orWhereHas('rawArticle.source', function ($sub) {
                      $sub->where('type', 'atom');
                  });
            })
            ->get();

        $breakingPublishedCount = 0;
        foreach ($breakingDrafts as $article) {
            $article->status = 'published';
            $article->published_at = now();
            $article->save();

            try {
                event(new \App\Events\ArticlePublished($article));
            } catch (\Exception $e) {
                Log::warning("PublishRateLimitedDrafts: event broadcast failed for Breaking Article {$article->id}");
            }
            $breakingPublishedCount++;
            $this->info("⚡ Published Breaking News/Release Article #{$article->id} (Bypassed limits): {$article->getTranslation('title', 'en')}");
        }

        // 2. PARSE AND CALCULATE DYNAMIC LIMITS (Strictly requires "min,max" ranges)
        // Daily Limit
        $daySetting = \App\Models\Setting::get('rate_limits.max_articles_per_day', '7,20');
        $dayParts = explode(',', $daySetting);
        $dayMin = (int) trim($dayParts[0] ?? 7);
        $dayMax = (int) trim($dayParts[1] ?? 20);
        mt_srand((int) date('Ymd'));
        $maxPerDay = mt_rand(min($dayMin, $dayMax), max($dayMin, $dayMax));
        mt_srand(); // Reset seed

        // Hourly Limit
        $hourSetting = \App\Models\Setting::get('rate_limits.max_articles_per_hour', '2,7');
        $hourParts = explode(',', $hourSetting);
        $hourMin = (int) trim($hourParts[0] ?? 2);
        $hourMax = (int) trim($hourParts[1] ?? 7);
        mt_srand((int) date('YmdH'));
        $maxPerHour = mt_rand(min($hourMin, $hourMax), max($hourMin, $hourMax));
        mt_srand(); // Reset seed

        $this->info("Dynamic Limits - Today's Limit: {$maxPerDay} | This Hour's Limit: {$maxPerHour}");

        // Count already published today (regular articles)
        $todayPublished = Article::where('status', 'published')
            ->whereDate('updated_at', today())
            ->count();

        $thisHourPublished = Article::where('status', 'published')
            ->where('updated_at', '>=', now()->subHour())
            ->count();

        $slotsAvailable = min(
            $maxPerDay - $todayPublished,
            $maxPerHour - $thisHourPublished
        );

        if ($slotsAvailable <= 0) {
            $this->info("No regular publishing slots available today ({$todayPublished}/{$maxPerDay} published, {$thisHourPublished}/{$maxPerHour} this hour).");
            // If we published breaking news, flush sitemap anyway
            if ($breakingPublishedCount > 0) {
                $this->flushSitemap();
            }
            return self::SUCCESS;
        }

        // Get regular drafts that are ready to publish (have images, passed validation, and are scheduled for now or in the past)
        $drafts = Article::where('status', 'draft')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('ai_metadata->needs_images')
                  ->orWhere('ai_metadata->needs_images', false);
            })
            // Exclude breaking news from standard slot calculations if they were already processed
            ->where(function ($q) {
                $q->whereNull('ai_metadata->importance')
                  ->orWhere('ai_metadata->importance', '<', 9);
            })
            ->whereDoesntHave('rawArticle.source', function ($sub) {
                $sub->where('type', 'atom');
            })
            ->orderBy('published_at', 'asc')
            ->limit($slotsAvailable)
            ->get();

        if ($drafts->isEmpty()) {
            $this->info("No regular pending drafts to publish.");
            if ($breakingPublishedCount > 0) {
                $this->flushSitemap();
            }
            return self::SUCCESS;
        }

        $published = 0;
        foreach ($drafts as $article) {
            // Category Limit (seeded dynamically for this category today, expects min,max)
            $catSetting = \App\Models\Setting::get('rate_limits.max_articles_per_category_per_day', '1,5');
            $catParts = explode(',', $catSetting);
            $catMin = (int) trim($catParts[0] ?? 1);
            $catMax = (int) trim($catParts[1] ?? 5);
            mt_srand((int) date('Ymd') + (int)$article->category_id);
            $catLimit = mt_rand(min($catMin, $catMax), max($catMin, $catMax));
            mt_srand(); // Reset seed

            $categoryToday = Article::where('status', 'published')
                ->where('category_id', $article->category_id)
                ->whereDate('updated_at', today())
                ->count();

            if ($categoryToday >= $catLimit) {
                $this->info("Skipped Article #{$article->id} - Category limit reached ({$categoryToday}/{$catLimit})");
                continue;
            }

            $article->status = 'published';
            $article->save();

            // Trigger realtime event
            try {
                event(new \App\Events\ArticlePublished($article));
            } catch (\Exception $e) {
                Log::warning("PublishRateLimitedDrafts: event broadcast failed for Article {$article->id}");
            }

            $published++;
            $this->info("Published Article #{$article->id}: {$article->getTranslation('title', 'en')}");

            if ($published >= $slotsAvailable) {
                break;
            }
        }

        if ($published > 0 || $breakingPublishedCount > 0) {
            $this->flushSitemap();
            $this->info("Published {$published} regular draft(s) and {$breakingPublishedCount} breaking news.");
            Log::info("PublishRateLimitedDrafts: published {$published} regular and {$breakingPublishedCount} breaking.");
        }

        return self::SUCCESS;
    }

    /**
     * Helper to flush the sitemap cache
     */
    private function flushSitemap(): void
    {
        try {
            \App\Http\Controllers\SitemapController::flushCache();
        } catch (\Exception $e) {
            // Sitemap controller may not exist in all environments
        }
    }
}
