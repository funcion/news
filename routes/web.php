<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Multilingual Routes
|
| Primary language (English) has NO prefix: /news/my-article
| Spanish has /es/ prefix: /es/noticias/mi-articulo
|--------------------------------------------------------------------------
*/

Route::group([
    'prefix'     => LaravelLocalization::setLocale(),
    'middleware' => [
        'localeSessionRedirect',
        'localizationRedirect',
        'localeViewPath',
    ],
], function () {

    // --- PUBLIC ROUTES ---
    Route::get('/', [\App\Http\Controllers\FrontendController::class, 'home'])->name('home');

    // Tags (Must be before root slugs to avoid collisions)
    Route::get('/tag/{slug}', [\App\Http\Controllers\FrontendController::class, 'tag'])->name('tags.show');

    // Root-level slugs (Articles & Categories)
    // The FrontendController intelligently resolves if the slug belongs to an article or a category
    Route::get('/{slug}', [\App\Http\Controllers\FrontendController::class, 'article'])->name('articles.show');

});

// Health check (no locale prefix needed)
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Sitemap Index (sitemap.xml = index of all sub-sitemaps)
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-articles-en.xml', [\App\Http\Controllers\SitemapController::class, 'articlesEn'])->name('sitemap.articles.en');
Route::get('/sitemap-articles-es.xml', [\App\Http\Controllers\SitemapController::class, 'articlesEs'])->name('sitemap.articles.es');
Route::get('/sitemap-categories.xml', [\App\Http\Controllers\SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-tags.xml', [\App\Http\Controllers\SitemapController::class, 'tags'])->name('sitemap.tags');
Route::get('/sitemap-news.xml', [\App\Http\Controllers\SitemapController::class, 'news'])->name('sitemap.news');
Route::get('/sitemap-images.xml', [\App\Http\Controllers\SitemapController::class, 'images'])->name('sitemap.images');

// IndexNow verification endpoint (Bing/Yandex send GET with ?key=xxx to verify ownership)
Route::get('/indexnow', [\App\Http\Controllers\IndexNowController::class, 'handle'])->name('indexnow');

// RSS Feed
Route::get('/feed.xml', [\App\Http\Controllers\FrontendController::class, 'feed'])->name('feed');
