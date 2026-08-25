<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Models Pool for Article Writing (Dynamic Rotation & Failover)
    |--------------------------------------------------------------------------
    |
    | List of OpenRouter model slugs to use for generating bilingual news.
    | The system will randomly pick from this pool for each article, providing
    | varied editorial voices, and will automatically failover to other models
    | in the pool if a model experiences downtime, rate limits, or timeouts.
    |
    | You can add or remove any OpenRouter model slug directly in this array
    | or by setting the AI_MODELS_POOL environment variable (comma-separated).
    |
    */
    'pool' => array_values(array_filter(array_map('trim', explode(',', env('AI_MODELS_POOL', implode(',', [
        'deepseek/deepseek-chat',
        'qwen/qwen3.7-flash',
        'google/gemini-2.5-flash',
        'minimax/minimax-m3',
    ])))))),

    /*
    |--------------------------------------------------------------------------
    | Maximum Output Tokens (max_tokens)
    |--------------------------------------------------------------------------
    |
    | Safe token limit per completion request. Setting this to 10,000 tokens
    | ensures ample capacity for full bilingual articles (EN & ES) with deep
    | analysis, quotes, and metadata, while preventing OpenRouter from
    | locking account credits against extreme default context windows (65k+).
    |
    */
    'max_tokens' => (int) env('AI_MAX_TOKENS', 10000),

    /*
    |--------------------------------------------------------------------------
    | Fast Classification Max Tokens
    |--------------------------------------------------------------------------
    |
    | Max tokens for the initial quick relevance & categorization step.
    |
    */
    'classification_max_tokens' => (int) env('AI_CLASSIFICATION_MAX_TOKENS', 1500),

    /*
    |--------------------------------------------------------------------------
    | Default Generation Temperature
    |--------------------------------------------------------------------------
    |
    | 0.7 offers the ideal balance between factual accuracy and natural,
    | engaging journalistic prose.
    |
    */
    'temperature' => 0.7,

    /*
    |--------------------------------------------------------------------------
    | Request Timeout (Seconds)
    |--------------------------------------------------------------------------
    |
    | Maximum seconds to wait for a completion before triggering failover.
    |
    */
    'timeout' => (int) env('AI_REQUEST_TIMEOUT', 180),
];