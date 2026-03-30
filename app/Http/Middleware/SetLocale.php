<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if a language segment exists
        $locale = $request->segment(1);

        if (in_array($locale, ['en', 'es'])) {
            App::setLocale($locale);
        } else {
            // Default to 'en'
            App::setLocale('en');
        }

        return $next($request);
    }
}
