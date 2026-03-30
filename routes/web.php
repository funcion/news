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
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    // Articles
    Route::get('/news/{slug}', function (string $slug) {
        // Controller to be created when the public frontend is built
        return response()->json(['slug' => $slug, 'locale' => app()->getLocale()]);
    })->name('articles.show');

    // Categories
    Route::get('/category/{slug}', function (string $slug) {
        return response()->json(['slug' => $slug, 'locale' => app()->getLocale()]);
    })->name('categories.show');

    // Tags
    Route::get('/tag/{slug}', function (string $slug) {
        return response()->json(['slug' => $slug, 'locale' => app()->getLocale()]);
    })->name('tags.show');

});

// Health check (no locale prefix needed)
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});