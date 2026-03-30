<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Locales
    |--------------------------------------------------------------------------
    | English is the primary language (no URL prefix).
    | Spanish gets the /es/ prefix.
    */

    'supportedLocales' => [
        'en' => [
            'name'       => 'English',
            'script'     => 'Latn',
            'native'     => 'English',
            'regional'   => 'en_US',
            'hreflang'   => 'en',
            'dateFormat' => 'MM/DD/YYYY',
            'windows'    => 'en-US',
            'direction'  => 'ltr',
        ],
        'es' => [
            'name'       => 'Español',
            'script'     => 'Latn',
            'native'     => 'Español',
            'regional'   => 'es_ES',
            'hreflang'   => 'es',
            'dateFormat' => 'DD/MM/YYYY',
            'windows'    => 'es-ES',
            'direction'  => 'ltr',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale Configuration
    |--------------------------------------------------------------------------
    */

    // Hide the default locale (English) from the URL prefix
    'hideDefaultLocaleInURL' => true,

    // Default locale
    'defaultLocale' => 'en',

    // Detect locale from Accept-Language header if no URL prefix is set
    'useAcceptLanguageHeader' => true,

    // Allow browser language negotiation
    'useCountryAsMissing' => false,

    /*
    |--------------------------------------------------------------------------
    | Ignored URLs
    |--------------------------------------------------------------------------
    | These URLs will NOT be prefixed with the locale.
    */
    'ignoredUrls' => [
        '/',
        '/health',
        '/admin*',   // Keep Filament admin without locale prefix
        '/up',
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Keys
    |--------------------------------------------------------------------------
    | Map locale codes to URL segments.
    | This allows /en/news to resolve as /news (hidden) and /es/noticias.
    */
    'localesMapping' => [],

    'urlsIgnored' => [
        '/skipped',
    ],

    'skipResponsiveRedirection' => false,

];
