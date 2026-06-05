<?php

namespace App\Providers;

use App\Models\RawArticle;
use App\Observers\RawArticleObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RawArticle::observe(RawArticleObserver::class);

        // Set the primary locale to English
        App::setLocale(config('app.locale', 'en'));

        // Purge Cloudflare CDN Cache whenever a Media file is deleted from R2
        \Spatie\MediaLibrary\MediaCollections\Models\Media::deleting(function ($media) {
            if ($media->disk === 'r2') {
                $urlsToPurge = [];
                try {
                    $urlsToPurge[] = $media->getUrl();
                    $urlsToPurge[] = $media->getUrl('thumb');
                    $urlsToPurge[] = $media->getUrl('medium');
                    $urlsToPurge[] = $media->getUrl('large');
                } catch (\Exception $e) {
                    // Evitar que falle la eliminación si hay problemas generando las URLs
                }

                if (!empty($urlsToPurge)) {
                    \App\Jobs\PurgeR2CacheJob::dispatch($urlsToPurge);
                }
            }
        });
    }
}
