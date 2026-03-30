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

    // Articles
    Route::get('/news/{slug}', [\App\Http\Controllers\FrontendController::class, 'article'])->name('articles.show');

    // Categories
    Route::get('/category/{slug}', [\App\Http\Controllers\FrontendController::class, 'category'])->name('categories.show');

    // Tags
    Route::get('/tag/{slug}', [\App\Http\Controllers\FrontendController::class, 'tag'])->name('tags.show');

});

// Health check (no locale prefix needed)
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});