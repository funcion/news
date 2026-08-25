<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled articles whose published_at date has arrived';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();
        $articles = Article::where('status', 'scheduled')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->get();

        if ($articles->isEmpty()) {
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($articles as $article) {
            $article->update([
                'status' => 'published',
            ]);

            // Broadcast realtime event
            try {
                event(new \App\Events\ArticlePublished($article));
            } catch (\Throwable $e) {
                Log::warning("Realtime broadcast failed for scheduled Article {$article->id}: " . $e->getMessage());
            }

            // Flush sitemap cache & trigger IndexNow ping
            try {
                \App\Http\Controllers\SitemapController::flushCache();
                if ($article->slug_en) {
                    \App\Http\Controllers\IndexNowController::ping(url('/' . $article->slug_en));
                }
                if ($article->slug_es) {
                    \App\Http\Controllers\IndexNowController::ping(url('/es/' . $article->slug_es));
                }
            } catch (\Throwable $e) {
                Log::warning("Sitemap/IndexNow failed for scheduled Article {$article->id}: " . $e->getMessage());
            }

            Log::info("Article {$article->id} ('{$article->title}') automatically published by scheduler at {$now->toDateTimeString()}.");
            $count++;
        }

        $this->info("Published {$count} scheduled articles.");
        return Command::SUCCESS;
    }
}