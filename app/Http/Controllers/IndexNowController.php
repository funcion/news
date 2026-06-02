<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowController extends Controller
{
    /**
     * IndexNow ping — notifies search engines that a URL has been updated.
     * Called after article publish/update.
     */
    public static function ping(string $url, string $host = null): void
    {
        $apiKey = config('services.indexnow.key', '');
        if (empty($apiKey)) {
            Log::warning('IndexNow: No API key configured');
            return;
        }

        $host = $host ?? parse_url(config('app.url'), PHP_URL_HOST);

        $payload = [
            'host'   => $host,
            'key'    => $apiKey,
            'url'    => $url,
            'urls'   => [$url],
        ];

        // Ping Bing/Yandex IndexNow endpoint
        $endpoints = [
            'https://api.indexnow.org/indexnow',
            "https://www.bing.com/indexnow",
            "https://yandex.com/indexnow",
        ];

        foreach ($endpoints as $endpoint) {
            try {
                $response = Http::timeout(5)->post($endpoint, $payload);
                Log::info("IndexNow ping sent to {$endpoint}: {$response->status()}");
            } catch (\Exception $e) {
                Log::warning("IndexNow ping failed for {$endpoint}: {$e->getMessage()}");
            }
        }
    }
}
