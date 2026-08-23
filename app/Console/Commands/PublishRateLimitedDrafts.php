<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishRateLimitedDrafts extends Command
{
    protected $signature = 'articles:publish-pending';
    protected $description = 'Publish all draft articles immediately without rate limits.';

    public function handle(): int
    {
        $drafts = Article::where('status', 'draft')
            ->where(function ($q) {
                $q->whereNull('ai_metadata->needs_images')
                  ->orWhere('ai_metadata->needs_images', false);
            })
            ->get();

        if ($drafts->isEmpty()) {
            $this->info("No pending drafts to publish.");
            return self::SUCCESS;
        }

        $publishedCount = 0;
        foreach ($drafts as $article) {
            $article->status = 'published';
            $article->published_at = $article->published_at ?? now();
            $article->save();

            try {
                event(new \App\Events\ArticlePublished($article));
            } catch (\Exception $e) {
                Log::warning("PublishPendingDrafts: event broadcast failed for Article {$article->id}: " . $e->getMessage());
            }

            $publishedCount++;
            $this->info("Published Article #{$article->id}: {$article->getTranslation('title', 'en')}");
        }

        if ($publishedCount > 0) {
            try {
                \App\Http\Controllers\SitemapController::flushCache();
            } catch (\Exception $e) {
                // Ignore if not present
            }
            $this->info("Successfully published {$publishedCount} draft article(s).");
            Log::info("Published {$publishedCount} draft article(s).");
        }

        return self::SUCCESS;
    }
}