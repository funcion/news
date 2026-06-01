<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for setting the top-level service credentials for services
    | which may require a free API key or secret to use normally.
    |
    */

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
    ],

];