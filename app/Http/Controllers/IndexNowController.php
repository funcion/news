<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SitemapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowController extends Controller
{
    /**
     * Handle incoming IndexNow verification request.
     */
    public function handle(Request $request)
    {
        $apiKey = config('services.indexnow.key', '');
        if (empty($apiKey)) {
            return response('IndexNow not configured', 404);
        }

        // IndexNow sends the key as a query param or in the body for verification
        $providedKey = $request->query('key', $request->input('key', ''));
        if ($providedKey === $apiKey) {
            return response($apiKey, 200)->header('Content-Type', 'text/plain');
        }

        return response('Invalid key', 403);
    }

    /**
     * Ping IndexNow endpoints after article publish/update.
     * Call: IndexNowController::ping($articleUrl)
     */
    public static function ping(string $url, string $host = null): void
    {
        $apiKey = config('services.indexnow.key', '');
        if (empty($apiKey)) {
            Log::info('IndexNow: No API key configured, skipping ping');
            return;
        }

        $host = $host ?? parse_url(config('app.url'), PHP_URL_HOST);

        $payload = [
            'host'   => $host,
            'key'    => $apiKey,
            'url'    => $url,
            'urls'   => [$url],
        ];

        $endpoints = [
            'https://api.indexnow.org/indexnow',
            'https://www.bing.com/indexnow',
            'https://yandex.com/indexnow',
        ];

        foreach ($endpoints as $endpoint) {
            try {
                $response = Http::timeout(5)->post($endpoint, $payload);
                Log::info("IndexNow ping → {$endpoint}: {$response->status()}");
            } catch (\Exception $e) {
                Log::warning("IndexNow ping failed → {$endpoint}: {$e->getMessage()}");
            }
        }

        // Also flush sitemap cache so next request regenerates it
        SitemapController::flushCache();
    }
}
