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
        // Read from DB (settable via Filament SettingsPage), fall back to config
        $maxPerDay  = (int) (\App\Models\Setting::get('rate_limits.max_articles_per_day', config('global.rate_limits.max_articles_per_day', 8)));
        $maxPerHour = (int) (\App\Models\Setting::get('rate_limits.max_articles_per_hour', config('global.rate_limits.max_articles_per_hour', 2)));
        $start      = (int) (\App\Models\Setting::get('rate_limits.publish_hour_start', config('global.rate_limits.publishing_hours.start', 7)));
        $end        = (int) (\App\Models\Setting::get('rate_limits.publish_hour_end', config('global.rate_limits.publishing_hours.end', 22)));
        $maxPerCategory = (int) (\App\Models\Setting::get('rate_limits.max_articles_per_category_per_day', config('global.rate_limits.max_articles_per_category_per_day', 3)));

        $hour = now()->hour;
        if ($hour < $start || $hour >= $end) {
            $this->info("Outside publishing hours ({$hour}:00). Skipping.");
            return self::SUCCESS;
        }

        // Count already published today
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
            $this->info("No publishing slots available today ({$todayPublished}/{$maxPerDay} published, {$thisHourPublished}/{$maxPerHour} this hour).");
            return self::SUCCESS;
        }

        // Get drafts that are ready to publish (have images, passed validation)
        $drafts = Article::where('status', 'draft')
            ->whereNotNull('published_at')
            ->where(function ($q) {
                $q->whereNull('ai_metadata->needs_images')
                  ->orWhere('ai_metadata->needs_images', false);
            })
            ->orderBy('published_at', 'asc')
            ->limit($slotsAvailable)
            ->get();

        if ($drafts->isEmpty()) {
            $this->info("No pending drafts to publish.");
            return self::SUCCESS;
        }

        $published = 0;
        foreach ($drafts as $article) {
            // Check category rate limit — use $maxPerCategory from DB settings
            // Read again in case it changed during run
            $catLimit = (int) (\App\Models\Setting::get('rate_limits.max_articles_per_category_per_day', $maxPerCategory));
            $categoryToday = Article::where('status', 'published')
                ->where('category_id', $article->category_id)
                ->whereDate('updated_at', today())
                ->count();

            if ($categoryToday >= $catLimit) {
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

        // Flush sitemap cache after publishing
        if ($published > 0) {
            try {
                \App\Http\Controllers\SitemapController::flushCache();
            } catch (\Exception $e) {
                // Sitemap controller may not exist in all environments
            }
            $this->info("Published {$published} rate-limited draft(s).");
            Log::info("PublishRateLimitedDrafts: published {$published} articles.");
        }

        return self::SUCCESS;
    }
}
