<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenRouter API Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials for OpenRouter AI API used by the article processing pipeline.
    | Uses config() instead of env() to work correctly with config:cache.
    |
    */

    'api_key' => env('OPENROUTER_API_KEY', ''),

    'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),

];
