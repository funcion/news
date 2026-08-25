<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Primary AI Model
    |--------------------------------------------------------------------------
    | The main model used across all services (redaction, classification, tags).
    */
    'default' => env('AI_DEFAULT_MODEL', 'deepseek/deepseek-v4-flash-0731'),

    /*
    |--------------------------------------------------------------------------
    | AI Models Pool (Failover Chain)
    |--------------------------------------------------------------------------
    | Ordered list of active models. If the primary model fails or times out,
    | the system automatically fails over in sequence to the next model.
    */
    'pool' => array_values(array_filter(
        explode(',', env('AI_MODELS_POOL', 'deepseek/deepseek-v4-flash-0731,qwen/qwen3.7-flash,deepseek/deepseek-chat'))
    )),

    /*
    |--------------------------------------------------------------------------
    | Centralized UI Metadata (Badges & Names)
    |--------------------------------------------------------------------------
    | Dynamic registry of models, human-friendly names and Filament badge colors.
    */
    'models' => [
        'deepseek/deepseek-v4-flash-0731' => [
            'name'  => 'DeepSeek V4 Flash',
            'color' => 'success',
        ],
        'qwen/qwen3.7-flash' => [
            'name'  => 'Qwen 3.7 Flash',
            'color' => 'warning',
        ],
        'deepseek/deepseek-chat' => [
            'name'  => 'DeepSeek V3',
            'color' => 'info',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Limits & Performance Tuners
    |--------------------------------------------------------------------------
    */
    'max_tokens'                => (int) env('AI_MAX_TOKENS', 10000),
    'classification_max_tokens' => (int) env('AI_CLASSIFICATION_MAX_TOKENS', 1500),
    'tag_max_tokens'            => (int) env('AI_TAG_MAX_TOKENS', 500),
    'temperature'               => (float) env('AI_TEMPERATURE', 0.7),
    'timeout'                   => (int) env('AI_TIMEOUT', 180),
];
