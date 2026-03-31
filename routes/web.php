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