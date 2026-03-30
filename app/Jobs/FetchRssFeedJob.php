<?php

namespace App\Jobs;

use App\Models\Source;
use App\Services\RssService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchRssFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Source $source
    ) {}

    /**
     * Execute the job.
     */
    public function handle(\App\Services\RssService $rssService, \App\Services\ScraperService $scraperService): void
    {
        try {
            Log::info("Fetching content for source: {$this->source->name} (Type: {$this->source->type})");
            
            if ($this->source->type === 'scraping') {
                $count = $scraperService->fetchSource($this->source);
            } else {
                $count = $rssService->fetchSource($this->source);
            }
            
            Log::info("Fetched {$count} new items for source: {$this->source->name}");
        } catch (\Exception $e) {
            Log::error("Error fetching source {$this->source->name}: " . $e->getMessage());
            $this->source->increment('score', -1);
            throw $e;
        }
    }
}
