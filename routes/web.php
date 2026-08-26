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
Route::get('/api/search', [\App\Http\Controllers\FrontendController::class, 'liveSearch'])->name('api.search');

// Newsletter (Double Opt-In & Unsubscribe)
Route::post('/newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/verify/{token}', [\App\Http\Controllers\NewsletterController::class, 'verify'])->name('newsletter.verify');
Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');



// Socialite OAuth Routes
Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
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

    // About Us & Editorial Standards (Bilingual URLs)
    Route::get('/about', [\App\Http\Controllers\FrontendController::class, 'about'])->name('about');
    Route::get('/nosotros', [\App\Http\Controllers\FrontendController::class, 'about'])->name('about.es');
    
    // Contact Us (Bilingual URLs)
    Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact.show');
    Route::get('/contacto', [\App\Http\Controllers\ContactController::class, 'show'])->name('contact.es');
    Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:5,1');
    Route::post('/contacto', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit.es')->middleware('throttle:5,1');

    // Legal and Editorial Transparency Routes (Bilingual URLs)
    Route::get('/terms', [\App\Http\Controllers\LegalController::class, 'terms'])->name('legal.terms');
    Route::get('/terminos', [\App\Http\Controllers\LegalController::class, 'terms'])->name('legal.terms.es');
    Route::get('/privacy', [\App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy');
    Route::get('/privacidad', [\App\Http\Controllers\LegalController::class, 'privacy'])->name('legal.privacy.es');
    Route::get('/cookies', [\App\Http\Controllers\LegalController::class, 'cookies'])->name('legal.cookies');
    Route::get('/editorial-policy', [\App\Http\Controllers\LegalController::class, 'editorialPolicy'])->name('legal.editorial');
    Route::get('/politica-editorial', [\App\Http\Controllers\LegalController::class, 'editorialPolicy'])->name('legal.editorial.es');

    // Email Verification Route (Signed URL with Auto-Login on Click)
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request, $id, $hash) {
        $user = \App\Models\User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        // Auto-login the user immediately upon clicking the verification link
        \Illuminate\Support\Facades\Auth::login($user, true);

        return redirect(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/'))
            ->with('status', __('ui.auth_email_verified_success'));
    })->middleware(['signed'])->name('verification.verify');

    // Resend Email Verification Route
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $email = $request->input('email');
        $user = \App\Models\User::where('email', $email)->first();
        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }
        return back()->with('status', __('ui.auth_verification_link_sent'));
    })->middleware(['throttle:6,1'])->name('verification.send');

    // --- AUTHENTICATION ROUTES (Livewire 4 + Fortify Headless) ---
    Route::middleware('guest')->group(function () {
        Route::get('/login', \App\Livewire\Auth\LoginComponent::class)->name('login');
        Route::get('/register', \App\Livewire\Auth\RegisterComponent::class)->name('register');
        Route::get('/forgot-password', \App\Livewire\Auth\ForgotPasswordComponent::class)->name('password.request');
        Route::get('/reset-password/{token}', \App\Livewire\Auth\ResetPasswordComponent::class)->name('password.reset');
    });

    Route::post('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect(\Mcamara\LaravelLocalization\Facades\LaravelLocalization::localizeUrl('/'));
    })->name('logout')->middleware('auth');

    // Tags (Must be before root slugs to avoid collisions)
    Route::get('/search', [\App\Http\Controllers\FrontendController::class, 'search'])->name('search');
    Route::get('/tag/{slug}', [\App\Http\Controllers\FrontendController::class, 'tag'])->name('tags.show');

    // Root-level slugs (Articles & Categories)
    // The FrontendController intelligently resolves if the slug belongs to an article or a category
    Route::get('/{slug}', [\App\Http\Controllers\FrontendController::class, 'article'])->name('articles.show');

});
