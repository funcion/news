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
    }
}
