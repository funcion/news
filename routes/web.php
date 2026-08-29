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

// IndexNow verification endpoint (Bing, Yandex, Copilot, ChatGPT Search)
Route::get('/indexnow', [\App\Http\Controllers\IndexNowController::class, 'handle'])->name('indexnow');
Route::get('/{key}.txt', function (string $key) {
    $apiKey = config('services.indexnow.key', '');
    if (!empty($apiKey) && $key === $apiKey) {
        return response($apiKey, 200)->header('Content-Type', 'text/plain');
    }
    abort(404);
})->where('key', '^[a-fA-F0-9]{8,128}$');

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

        // --- BILINGUAL INSTITUTIONAL ROUTES ---
    
            // --- USER PROFILE & ACCOUNT ROUTES (PROTECTED) ---
    Route::group(['middleware' => ['auth']], function () {
        Route::get('/profile', function () {
            if (app()->getLocale() === 'es') {
                return redirect('/es/perfil', 301);
            }
            return app(\App\Http\Controllers\ProfileController::class)->show();
        })->name('profile');

        Route::get('/perfil', function () {
            if (app()->getLocale() !== 'es') {
                return redirect('/profile', 301);
            }
            return app(\App\Http\Controllers\ProfileController::class)->show();
        })->name('profile.es');

        Route::post('/profile/info', [\App\Http\Controllers\ProfileController::class, 'updateInfo'])->name('profile.info');
        Route::post('/perfil/info', [\App\Http\Controllers\ProfileController::class, 'updateInfo'])->name('profile.info.es');

        Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/perfil/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.es');
    });

    // Categories Index (/categories in EN, /es/categorias in ES)
    Route::get('/categories', function () {
        if (app()->getLocale() === 'es') {
            return redirect('/es/categorias', 301);
        }
        return app(\App\Http\Controllers\FrontendController::class)->categories();
    })->name('categories');

    Route::get('/categorias', function () {
        if (app()->getLocale() !== 'es') {
            return redirect('/categories', 301);
        }
        return app(\App\Http\Controllers\FrontendController::class)->categories();
    })->name('categories.es');

    // About Us (/about-us in EN, /es/nosotros in ES)
    Route::get('/about-us', function () {
        if (app()->getLocale() === 'es') {
            return redirect('/es/nosotros', 301);
        }
        return app(\App\Http\Controllers\FrontendController::class)->about();
    })->name('about');

    Route::get('/about', function () {
        return redirect(app()->getLocale() === 'es' ? '/es/nosotros' : '/about-us', 301);
    });

    Route::get('/nosotros', function () {
        if (app()->getLocale() !== 'es') {
            return redirect('/about-us', 301);
        }
        return app(\App\Http\Controllers\FrontendController::class)->about();
    })->name('about.es');

    // Contact (/contact in EN, /es/contacto in ES)
    Route::get('/contact', function () {
        if (app()->getLocale() === 'es') {
            return redirect('/es/contacto', 301);
        }
        return app(\App\Http\Controllers\ContactController::class)->show();
    })->name('contact.show');

    Route::get('/contacto', function () {
        if (app()->getLocale() !== 'es') {
            return redirect('/contact', 301);
        }
        return app(\App\Http\Controllers\ContactController::class)->show();
    })->name('contact.es');

    Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit')->middleware('throttle:5,1');
    Route::post('/contacto', [\App\Http\Controllers\ContactController::class, 'submit'])->name('contact.submit.es')->middleware('throttle:5,1');

    // --- LEGAL & TRANSPARENCY BILINGUAL ROUTES ---

    // 1. Terms & Conditions (/terms-and-conditions in EN, /es/terminos-y-condiciones in ES)
    Route::get('/terms-and-conditions', function () {
        if (app()->getLocale() === 'es') {
            return redirect('/es/terminos-y-condiciones', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->terms();
    })->name('legal.terms');

    Route::get('/terminos-y-condiciones', function () {
        if (app()->getLocale() !== 'es') {
            return redirect('/terms-and-conditions', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->terms();
    })->name('legal.terms.es');

    // Legacy redirects for terms
    Route::get('/terms', fn() => redirect(app()->getLocale() === 'es' ? '/es/terminos-y-condiciones' : '/terms-and-conditions', 301));
    Route::get('/terminos', fn() => redirect(app()->getLocale() === 'es' ? '/es/terminos-y-condiciones' : '/terms-and-conditions', 301));
    Route::get('/terms-of-service', fn() => redirect(app()->getLocale() === 'es' ? '/es/terminos-y-condiciones' : '/terms-and-conditions', 301));
    Route::get('/terminos-de-servicio', fn() => redirect(app()->getLocale() === 'es' ? '/es/terminos-y-condiciones' : '/terms-and-conditions', 301));

    // 2. Privacy Policy (/privacy-policy in EN, /es/politica-de-privacidad in ES)
    Route::get('/privacy-policy', function () {
        if (app()->getLocale() === 'es') {
            return redirect('/es/politica-de-privacidad', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->privacy();
    })->name('legal.privacy');

    Route::get('/politica-de-privacidad', function () {
        if (app()->getLocale() !== 'es') {
            return redirect('/privacy-policy', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->privacy();
    })->name('legal.privacy.es');

    // Legacy redirects for privacy
    Route::get('/privacy', fn() => redirect(app()->getLocale() === 'es' ? '/es/politica-de-privacidad' : '/privacy-policy', 301));
    Route::get('/privacidad', fn() => redirect(app()->getLocale() === 'es' ? '/es/politica-de-privacidad' : '/privacy-policy', 301));

    // 3. Cookie Policy (/cookie-policy in EN, /es/politica-de-cookies in ES)
    Route::get('/cookie-policy', function () {
        if (app()->getLocale() === 'es') {
            return redirect('/es/politica-de-cookies', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->cookies();
    })->name('legal.cookies');

    Route::get('/politica-de-cookies', function () {
        if (app()->getLocale() !== 'es') {
            return redirect('/cookie-policy', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->cookies();
    })->name('legal.cookies.es');

    // Legacy redirects for cookies
    Route::get('/cookies', fn() => redirect(app()->getLocale() === 'es' ? '/es/politica-de-cookies' : '/cookie-policy', 301));

    // 4. Editorial Policy (/editorial-policy in EN, /es/politica-editorial in ES)
    Route::get('/editorial-policy', function () {
        if (app()->getLocale() === 'es') {
            return redirect('/es/politica-editorial', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->editorialPolicy();
    })->name('legal.editorial');

    Route::get('/politica-editorial', function () {
        if (app()->getLocale() !== 'es') {
            return redirect('/editorial-policy', 301);
        }
        return app(\App\Http\Controllers\LegalController::class)->editorialPolicy();
    })->name('legal.editorial.es');

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
        // Search (/search in EN, /es/buscar in ES)
    Route::get('/search', function (\Illuminate\Http\Request $request) {
        if (app()->getLocale() === 'es') {
            return redirect('/es/buscar' . ($request->getQueryString() ? ('?' . $request->getQueryString()) : ''), 301);
        }
        return app(\App\Http\Controllers\FrontendController::class)->search($request);
    })->name('search');

    Route::get('/buscar', function (\Illuminate\Http\Request $request) {
        if (app()->getLocale() !== 'es') {
            return redirect('/search' . ($request->getQueryString() ? ('?' . $request->getQueryString()) : ''), 301);
        }
        return app(\App\Http\Controllers\FrontendController::class)->search($request);
    })->name('search.es');
    Route::get('/tag/{slug}', [\App\Http\Controllers\FrontendController::class, 'tag'])->name('tags.show');

    // Root-level slugs (Articles & Categories)
    // The FrontendController intelligently resolves if the slug belongs to an article or a category
    Route::get('/{slug}', [\App\Http\Controllers\FrontendController::class, 'article'])->name('articles.show');

});
