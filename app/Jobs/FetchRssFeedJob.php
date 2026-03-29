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
    public function handle(RssService $rssService): void
    {
        try {
            Log::info("Fetching feed for source: {$this->source->name} ({$this->source->url})");
            
            $count = $rssService->fetchSource($this->source);
            
            Log::info("Fetched {$count} new articles for source: {$this->source->name}");
        } catch (\Exception $e) {
            Log::error("Error fetching feed for source {$this->source->name}: " . $e->getMessage());
            $this->source->increment('score', -1);
            throw $e;
        }
    }
}
