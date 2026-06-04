<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MigrateMediaToR2 extends Command
{
    protected $signature = 'media:migrate-to-r2 
        {--dry-run : Show what would be migrated without uploading}
        {--batch-size=50 : Number of files per batch}
        {--skip-conversions : Skip conversion files (thumb/medium/large)}';

    protected $description = 'Migrate all media files from local disk to Cloudflare R2. Preserves originals and conversions.';

    public function handle(): int
    {
        $this->warn('╔══════════════════════════════════════════════════════════╗');
        $this->warn('║   CLOUDFLARE R2 MIGRATION — Media Files                 ║');
        $this->warn('╚══════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Pre-flight checks
        if (!$this->preflightChecks()) {
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch-size');
        $skipConversions = $this->option('skip-conversions');

        // Get all media records
        $totalMedia = Media::count();
        $this->info("📊 Total media records in database: {$totalMedia}");

        if ($totalMedia === 0) {
            $this->info('✅ No media to migrate.');
            return self::SUCCESS;
        }

        $localDisk = Storage::disk('public');
        $r2Disk = Storage::disk('r2');

        $migrated = 0;
        $skipped = 0;
        $failed = 0;
        $totalSize = 0;

        $bar = $this->output->createProgressBar($totalMedia);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->setMessage('Starting...');
        $bar->start();

        Media::query()->chunkById($batchSize, function ($mediaItems) use (
            $localDisk, $r2Disk, $dryRun, $skipConversions, $bar,
            &$migrated, &$skipped, &$failed, &$totalSize
        ) {
            foreach ($mediaItems as $media) {
                $bar->setMessage("Processing: {$media->file_name}");

                // 1. Migrate the original file
                $originalPath = $media->id . '/' . $media->file_name;
                $result = $this->migrateFile($localDisk, $r2Disk, $originalPath, $dryRun);

                if ($result === 'migrated') {
                    $migrated++;
                    $fileSize = $localDisk->size($originalPath);
                    $totalSize += $fileSize;
                } elseif ($result === 'skipped') {
                    $skipped++;
                } else {
                    $failed++;
                    $this->newLine();
                    $this->error("  ❌ Failed: {$originalPath}");
                }

                // 2. Migrate conversion files (thumb, medium, large)
                if (!$skipConversions) {
                    $conversionsPath = "conversions/{$media->id}";
                    if ($localDisk->exists($conversionsPath)) {
                        $conversionFiles = $localDisk->allFiles($conversionsPath);
                        foreach ($conversionFiles as $conversionFile) {
                            $convResult = $this->migrateFile($localDisk, $r2Disk, $conversionFile, $dryRun);
                            if ($convResult === 'migrated') {
                                $totalSize += $localDisk->size($conversionFile);
                            }
                        }
                    }
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->warn('╔══════════════════════════════════════════════════════════╗');
        $this->warn('║                    MIGRATION SUMMARY                    ║');
        $this->warn('╚══════════════════════════════════════════════════════════╝');
        $this->info("  📦 Files migrated:  {$migrated}");
        $this->info("  ⏭️  Files skipped:  {$skipped}");
        $this->info("  ❌ Files failed:    {$failed}");
        $this->info("  💾 Total size:      " . $this->formatBytes($totalSize));
        $this->newLine();

        if ($dryRun) {
            $this->warn('  ⚠️  DRY RUN — no files were actually uploaded.');
            $this->info('  Run without --dry-run to execute the migration.');
        } else {
            $this->info('  ✅ Migration complete!');
            $this->newLine();
            $this->warn('  NEXT STEPS:');
            $this->info('  1. Verify images load from R2: check a few article URLs');
            $this->info('  2. Set MEDIA_DISK=r2 in your .env');
            $this->info('  3. Clear config cache: php artisan config:clear');
            $this->info('  4. Test that new uploads go to R2');
            $this->info('  5. Once confirmed, you can delete local media:');
            $this->info('     rm -rf storage/app/public/media/*');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function preflightChecks(): bool
    {
        // Check R2 config
        $r2Endpoint = config('filesystems.disks.r2.endpoint');
        $r2Bucket = config('filesystems.disks.r2.bucket');
        $r2Key = config('filesystems.disks.r2.key');

        if (empty($r2Endpoint) || empty($r2Bucket) || empty($r2Key)) {
            $this->error('❌ R2 is not configured!');
            $this->newLine();
            $this->info('Please set these in your .env:');
            $this->info('  R2_ACCESS_KEY_ID=your_key');
            $this->info('  R2_SECRET_ACCESS_KEY=your_secret');
            $this->info('  R2_BUCKET=your_bucket_name');
            $this->info('  R2_ENDPOINT=https://{account_id}.r2.cloudflarestorage.com');
            $this->info('  R2_PUBLIC_URL=https://media.yourdomain.com');
            return false;
        }

        $this->info("✅ R2 configured:");
        $this->info("   Bucket:   {$r2Bucket}");
        $this->info("   Endpoint: {$r2Endpoint}");
        $this->info("   Public:   " . (config('filesystems.disks.r2.url') ?: 'NOT SET'));
        $this->newLine();

        // Test R2 connection
        $this->info('🔌 Testing R2 connection...');
        try {
            $r2Disk = Storage::disk('r2');
            $r2Disk->put('_health-check.txt', 'ok');
            $r2Disk->delete('_health-check.txt');
            $this->info('✅ R2 connection successful!');
        } catch (\Throwable $e) {
            $this->error("❌ R2 connection failed: {$e->getMessage()}");
            return false;
        }

        $this->newLine();

        // Confirm
        if (!$this->option('dry-run')) {
            return $this->confirm('This will upload ALL media files to R2. Continue?', false);
        }

        return true;
    }

    /**
     * Upload a single file from local to R2.
     *
     * @return string 'migrated', 'skipped', or 'failed'
     */
    private function migrateFile($localDisk, $r2Disk, string $path, bool $dryRun): string
    {
        // Check if file exists locally
        if (!$localDisk->exists($path)) {
            return 'skipped';
        }

        // Check if file already exists on R2 (skip re-upload)
        if (!$dryRun && $r2Disk->exists($path)) {
            return 'skipped';
        }

        if ($dryRun) {
            return 'migrated';
        }

        try {
            $contents = $localDisk->get($path);
            $mimeType = $localDisk->mimeType($path);

            // Upload to R2 with proper content type and public visibility
            $r2Disk->put($path, $contents, [
                'visibility' => 'public',
                'ContentType' => $mimeType,
                'CacheControl' => 'public, max-age=31536000, immutable',
            ]);

            return 'migrated';
        } catch (\Throwable $e) {
            Log::error("R2 migration failed for {$path}: " . $e->getMessage());
            return 'failed';
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = $bytes;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
