<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ForceLocale
{
    /**
     * Handle an incoming request.
     * Este middleware resuelve el bug de Octane donde el singleton de laravel-localization
     * se queda atascado en el último idioma registrado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Extraemos el prefijo del idioma de la URL (ej. 'es' o 'en')
        $locale = $request->segment(1);

        // Si el usuario está logueado, su preferencia manda (ideal para el panel de Filament)
        if (auth()->check() && !empty(auth()->user()->preferred_locale)) {
            $userLocale = auth()->user()->preferred_locale;
            app()->setLocale($userLocale);
            LaravelLocalization::setLocale($userLocale);
            return $next($request);
        }

        if ($locale && array_key_exists($locale, LaravelLocalization::getSupportedLocales())) {
            app()->setLocale($locale);
            LaravelLocalization::setLocale($locale);
        } else {
            // Rutas sin prefijo (ej. raíz '/'): Negociar con el navegador o usar por defecto
            $negotiated = LaravelLocalization::setLocale();
            app()->setLocale($negotiated ?: config('app.locale'));
        }

        return $next($request);
    }
}
