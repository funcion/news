<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TagsCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tags:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up unused tags and update article counts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting tags cleanup...');

        // 1. Recalculate article counts
        $this->info('Recalculating article counts...');
        Tag::chunk(200, function ($tags) {
            foreach ($tags as $tag) {
                $actualCount = $tag->articles()->count();
                if ($tag->article_count !== $actualCount) {
                    $tag->article_count = $actualCount;
                    $tag->save();
                }
            }
        });

        // 2. Remove tags with 0 articles that are older than 30 days
        $this->info('Removing unused tags older than 30 days...');
        $deleted = Tag::where('article_count', 0)
            ->where('updated_at', '<', now()->subDays(30))
            ->delete();

        $this->info("Deleted {$deleted} orphaned tags.");

        $this->info('Tags cleanup completed successfully.');
    }
}
