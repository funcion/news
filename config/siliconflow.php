<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SiliconFlow Image Generation Configuration
    |--------------------------------------------------------------------------
    |
    | Credentials for SiliconFlow AI image generation API.
    | Uses config() instead of env() to work correctly with config:cache.
    |
    */

    'api_key' => env('SILICONFLOW_API_KEY', ''),

    'image_model' => env('SILICONFLOW_IMAGE_MODEL', 'black-forest-labs/FLUX.1-schnell'),

];
