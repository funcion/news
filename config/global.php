<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Glodaxia Branding & Identity
    |--------------------------------------------------------------------------
    |
    | Global settings for the platform branding, meta titles and taglines.
    |
    */
    'site' => [
        'name' => env('APP_NAME', 'Glodaxia'),
        'tagline' => [
            'es' => 'Periodismo Tecnológico de Vanguardia',
            'en' => 'Next-Gen Technology Journalism',
        ],
        'description' => [
            'es' => 'Tu fuente diaria de noticias, análisis y tendencias sobre Inteligencia Artificial, desarrollo de software, ciberseguridad y tecnología global.',
            'en' => 'Your daily source for tech news, in-depth analysis, and emerging trends in AI, software development, cybersecurity, and global innovation.',
        ],
        'url' => env('APP_URL', 'http://localhost:8000'),
        'contact_email' => 'hi@glodaxia.com',
    ],

    /*
    |--------------------------------------------------------------------------
    | Editorial & AI Directives (Google E-E-A-T & Quality Standards)
    |--------------------------------------------------------------------------
    |
    | Rules for AI content generation, word counts, personas, and focus guidelines.
    |
    */
    'editorial' => [
        // Persona for the AI journalist
        'persona' => 'world-class Senior Technology Journalist and elite SEO copywriter (15+ years experience) working for Glodaxia, a premium tech publication.',

        // Strict grounding rules against hallucinations
        'focus_rules' => 'STRICTLY ADHERE TO THE FACTS PROVIDED. NEVER invent names, dates, statistics, or events not present in the SOURCE FACTS.',

        // Word count targets per content type (Strict minimum 700+ words to prevent thin content)
        'word_targets' => [
            'news'   => '700-1000 words EN | 700-1000 palabras ES (Mínimo estricto: 700 palabras)',
            'blog'   => '850-1300 words EN | 850-1300 palabras ES (Mínimo estricto: 850 palabras)',
            'guide'  => '1200-1800 words EN | 1200-1800 palabras ES (Mínimo estricto: 1200 palabras)',
            'review' => '900-1400 words EN | 900-1400 palabras ES (Mínimo estricto: 900 palabras)',
            'pillar' => '1600-2600 words EN | 1600-2600 palabras ES (Mínimo estricto: 1600 palabras)',
        ],

        // Default author slug if fallback is needed
        'default_author' => [
            'name' => 'Luis Figuera',
            'slug' => 'luis-figuera',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transparency & Feature Toggles
    |--------------------------------------------------------------------------
    |
    | Feature flags for frontend components and disclosure notices.
    |
    */
    'features' => [
        'show_source_links'   => true,  // Show canonical source link on articles
        'show_editorial_note' => true,  // Show "Written with technological support" disclosure
    ],
];
