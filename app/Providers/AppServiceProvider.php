<?php

namespace App\Providers;

use App\Models\RawArticle;
use App\Observers\RawArticleObserver;
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
    }
}
