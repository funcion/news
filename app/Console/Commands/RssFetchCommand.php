<?php

namespace App\Console\Commands;

use App\Jobs\FetchRssFeedJob;
use App\Models\Setting;
use App\Models\Source;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RssFetchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:fetch {--force : Force fetching all sources}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from RSS sources based on their frequency';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check Master Ingestion Switch
        if (! Setting::get('ingestion_enabled', true)) {
            $this->warn("⏸️ RSS Ingestion is currently paused by Master Switch in /admin/sources. Skipping all feeds.");
            return 0;
        }

        $force = $this->option('force');
        
        $sources = Source::query()
            ->where('is_active', true)
            ->when(!$force, function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('last_fetched_at')
                      ->orWhereRaw('last_fetched_at + (frequency * interval \'1 minute\') <= ?', [now()]);
                });
            })
            ->get();

        if ($sources->isEmpty()) {
            $this->info("No sources to fetch at this time.");
            return 0;
        }

        $this->info("Fetching " . $sources->count() . " active sources...");

        foreach ($sources as $source) {
            $this->info("Dispatching FetchRssFeedJob for source: {$source->name}");
            FetchRssFeedJob::dispatch($source);
        }

        $this->info("All jobs dispatched to queue.");
        
        return 0;
    }
}