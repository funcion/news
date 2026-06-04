<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CleanupOrphanMedia extends Command
{
    protected $signature = 'media:cleanup-orphan {--dry-run : Show what would be deleted without actually deleting}';
    protected $description = 'Delete media files for articles that no longer exist in the database. Works with both local disk and Cloudflare R2.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $diskName = config('media-library.disk_name', 'public');
        $isR2 = $diskName === 'r2';

        $this->warn('╔══════════════════════════════════════════════════════════╗');
        $this->warn('║   ORPHAN MEDIA CLEANUP                                  ║');
        $this->warn('╚══════════════════════════════════════════════════════════╝');
        $this->info("  Disk: {$diskName}" . ($isR2 ? ' (Cloudflare R2)' : ' (Local)'));
        $this->newLine();

        // Strategy: use Spatie Media model to find orphans (works with any disk)
        $existingArticleIds = DB::table('articles')->pluck('id')->toArray();
        $existingIdsLookup = array_flip($existingArticleIds);

        $orphanMedia = Media::where('model_type', 'App\\Models\\Article')
            ->whereNotIn('model_id', $existingArticleIds)
            ->get();

        $this->info("  Existing articles: " . count($existingArticleIds));
        $this->info("  Orphan media records: " . $orphanMedia->count());

        if ($orphanMedia->isEmpty()) {
            $this->info("\n✅ No orphan media found. Storage is clean.");
            return self::SUCCESS;
        }

        $deletedFiles = 0;
        $deletedRecords = 0;

        foreach ($orphanMedia as $media) {
            $originalPath = $media->id . '/' . $media->file_name;

            if ($dryRun) {
                $this->line("  [DRY RUN] Would delete: {$originalPath}");
            } else {
                // Use Spatie's built-in delete which handles disk cleanup
                try {
                    $media->delete(); // This removes the file from disk + DB record
                    $deletedRecords++;
                    $deletedFiles++;
                } catch (\Throwable $e) {
                    $this->error("  ❌ Failed to delete media #{$media->id}: {$e->getMessage()}");
                    Log::error("CleanupOrphanMedia failed for media #{$media->id}: " . $e->getMessage());
                }
            }
        }

        // For local disk, also clean up empty directories
        if (!$isR2 && !$dryRun) {
            $mediaPath = storage_path('app/public/media');
            $emptyDirs = 0;
            if (File::isDirectory($mediaPath)) {
                $directories = File::directories($mediaPath);
                foreach ($directories as $dir) {
                    if (basename($dir) === 'conversions') continue;
                    $allFiles = File::allFiles($dir);
                    if (empty($allFiles)) {
                        File::deleteDirectory($dir);
                        $emptyDirs++;
                    }
                }
            }
            if ($emptyDirs > 0) {
                $this->info("  Cleaned up {$emptyDirs} empty local directories.");
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN — no files were deleted.');
            $this->info('Run without --dry-run to execute cleanup.');
        } else {
            $this->info("✅ Done. Deleted {$deletedRecords} orphan media records and their files.");
            Log::info("CleanupOrphanMedia: deleted {$deletedRecords} orphan media records (disk: {$diskName}).");
        }

        return self::SUCCESS;
    }
}
