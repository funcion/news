<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PurgeR2CacheJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 30;
    public $tries = 2;

    public function __construct(
        protected array $urls
    ) {}

    public function handle(): void
    {
        $zoneId = env('CLOUDFLARE_ZONE_ID');
        $apiToken = env('CLOUDFLARE_API_TOKEN');

        if (empty($zoneId) || empty($apiToken)) {
            Log::warning('PurgeR2CacheJob: CLOUDFLARE_ZONE_ID or CLOUDFLARE_API_TOKEN not set. Skipping cache purge.');
            return;
        }

        // Cloudflare Purge Cache API — purge by URL
        // Docs: https://developers.cloudflare.com/api/operations/zone-purge-purge-files-by-url
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiToken}",
            'Content-Type' => 'application/json',
        ])->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/purge_cache", [
            'files' => $this->urls,
        ]);

        if ($response->successful() && ($response->json('success') ?? false)) {
            Log::info('PurgeR2CacheJob: Cache purged successfully.', [
                'urls_count' => count($this->urls),
            ]);
            return;
        }

        // 401 = permanent auth failure — don't waste retries
        if ($response->status() === 401) {
            Log::error('PurgeR2CacheJob: Cloudflare authentication failed (401). Check CLOUDFLARE_API_TOKEN. NOT retrying.', [
                'urls_count' => count($this->urls),
            ]);
            return; // Don't throw — prevents useless retries on permanent auth failure
        }

        Log::error('PurgeR2CacheJob: Cache purge failed.', [
            'status' => $response->status(),
            'body' => $response->body(),
            'urls' => $this->urls,
        ]);
    }
}
