<?php

namespace App\Console\Commands;

use App\Models\RawArticle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgePendingArticlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingestion:cancel-all {--delete : Delete raw articles permanently instead of marking as ignored}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel all pending AI article generations in queue and mark raw articles as ignored';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cancelling all pending AI generation jobs and raw articles...');

        // 1. Clear database jobs queue
        $jobsCleared = DB::table('jobs')->delete();
        $this->info("Cleared {$jobsCleared} pending jobs from queue.");

        // 2. Clear failed jobs
        $failedCleared = DB::table('failed_jobs')->delete();
        $this->info("Cleared {$failedCleared} failed jobs from history.");

        // 3. Update or delete pending raw articles
        if ($this->option('delete')) {
            $rawDeleted = RawArticle::whereIn('status', ['pending', 'processing'])->delete();
            $this->info("Permanently deleted {$rawDeleted} pending raw articles.");
        } else {
            $rawIgnored = RawArticle::whereIn('status', ['pending', 'processing'])
                ->update(['status' => 'ignored']);
            $this->info("Marked {$rawIgnored} pending raw articles as 'ignored'.");
        }

        $this->newLine();
        $this->info('All pending AI article generations have been successfully cancelled!');

        return Command::SUCCESS;
    }
}