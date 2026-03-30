<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     * Sets the app locale based on URL prefix or browser Accept-Language header.
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = LaravelLocalization::setLocale();

        if (!$locale) {
            $locale = config('app.locale', 'en');
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
