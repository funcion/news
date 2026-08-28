<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Glodaxia Branding & Identity
    |--------------------------------------------------------------------------
    | Global settings for the platform branding, meta titles and taglines.
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
        'url' => env('APP_URL', 'https://glodaxia.com'),
        'contact_email' => env('CONTACT_EMAIL', 'hi@glodaxia.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security & Super Administrators
    |--------------------------------------------------------------------------
    | Centralized list of emails with implicit root SuperAdmin access.
    | Protected against accidental deletion, full gate bypass in Filament.
    */
    'security' => [
        'super_admins' => array_values(array_filter(array_map('trim', explode(',', env(
            'SUPER_ADMIN_EMAILS',
            'sifuncion@gmail.com,admin@glodaxia.com,luis.figuera@glodaxia.com'
        ))))),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Storage & CDN
    |--------------------------------------------------------------------------
    | Cloudflare R2 public endpoint / CDN domain for assets and images.
    */
    'media' => [
        'public_url' => rtrim(env('R2_PUBLIC_URL', 'https://media.glodaxia.com'), '/'),
        'image_conversions' => [
            'thumb'  => ['width' => 480,  'height' => 270],
            'medium' => ['width' => 800,  'height' => 450],
            'large'  => ['width' => 1200, 'height' => 675],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Editorial & AI Directives (Google E-E-A-T & Quality Standards)
    |--------------------------------------------------------------------------
    | Rules for AI content generation, word counts, personas, and focus guidelines.
    */
    'editorial' => [
        // Persona for the AI journalist
        'persona' => 'world-class Senior Technology Journalist and elite SEO copywriter (15+ years experience) working for Glodaxia, a premium tech publication.',

        // Strict grounding rules against hallucinations
        'focus_rules' => 'STRICTLY ADHERE TO THE FACTS PROVIDED. NEVER invent names, dates, statistics, or events not present in the SOURCE FACTS.',

        // Default author fallback if no active author is found
        'default_author' => [
            'name' => [
                'es' => 'Luis Figuera',
                'en' => 'Luis Figuera',
            ],
            'email' => 'luis@glodaxia.com',
            'slug'  => 'luis-figuera',
            'bio'   => [
                'es' => 'Especialista en redacción y análisis tecnológico en Glodaxia.',
                'en' => 'Technology analysis and digital journalism specialist at Glodaxia.',
            ],
        ],

        // -------------------------------------------------------------------
        // Standard Character & Length Limits (Enforced programmatically)
        // -------------------------------------------------------------------
        'limits' => [
            'title' => [
                'min' => 60,
                'max' => 120,
            ],
            'excerpt' => [
                'min' => 160,
                'max' => 250,
            ],
            'meta_title' => [
                'min' => 40,
                'max' => 80,
            ],
            'meta_description' => [
                'min' => 120,
                'max' => 160,
            ],
            'image_alt' => [
                'max' => 125,
            ],
            'image_title' => [
                'max' => 70,
            ],
            'reading_time_wpm' => 200,
            'raw_preview_chars' => 2000,
            'min_words' => [
                'news'   => 700,
                'blog'   => 900,
                'guide'  => 1200,
                'review' => 900,
                'pillar' => 1600,
            ],
        ],

        // Word count targets per content type (for prompt context)
        'word_targets' => [
            'news'   => '700-1200 words EN | 700-1200 palabras ES (Mínimo estricto: 700 palabras)',
            'blog'   => '900-1500 words EN | 900-1500 palabras ES (Mínimo estricto: 900 palabras)',
            'guide'  => '1200-2000 words EN | 1200-2000 palabras ES (Mínimo estricto: 1200 palabras)',
            'review' => '900-1500 words EN | 900-1500 palabras ES (Mínimo estricto: 900 palabras)',
            'pillar' => '1600-2800 words EN | 1600-2800 palabras ES (Mínimo estricto: 1600 palabras)',
        ],

        // -------------------------------------------------------------------
        // Anti-AI Fingerprint Filter (Banned Clichés & Metaphors)
        // -------------------------------------------------------------------
        'blocked_phrases' => [
            'en' => [
                'paradigm shift', 'game-changer', 'revolutionary', 'democratization of',
                'inflection point', 'trajectory points toward', 'unprecedented scale',
                'seamlessly integrate', 'robust ecosystem', 'the digital landscape',
                'it remains to be seen', 'only time will tell', "it's worth noting",
                "in today's rapidly evolving", 'at the end of the day',
                'raises important questions', 'a bold step forward', 'double-edged sword',
                'the implications are profound', 'a testament to',
                "let's dive in", 'let me break this down', 'in my experience',
                'low-hanging fruit', 'home run', 'slam dunk', 'picture this',
                'beacon of hope', 'tapestry', 'delve', 'orchestrate', 'in conclusion',
            ],
            'es' => [
                'cambio de paradigma', 'en conclusión', 'sin lugar a dudas',
                'cabe destacar', 'queda por ver', 'un arma de doble filo',
                'marca un antes y un después', 'las implicaciones son profundas',
                'en el mundo de', 'sin ir más lejos',
                'como ya hemos mencionado', 'en última instancia',
                'es importante destacar',
                'sin duda alguna', 'no cabe duda', 'vale la pena mencionar',
                'el panorama digital', 'faro de esperanza', 'espada de doble filo',
                'tapiz', 'adentrarse', 'testimonio de', 'orquestar', 'al final del día',
                'solo el tiempo dirá', 'en el mundo actual',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Anti-Duplicate Detection Engine
    |--------------------------------------------------------------------------
    | Thresholds and parameters for the multi-tier duplicate prevention system.
    */
    'duplicate_detection' => [
        'event_slug_window_hours' => 36,
        'fuzzy_similarity_threshold' => 75,
        'semantic_exact_distance' => 0.18,
        'semantic_judge_distance' => 0.35,
        'search_window_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Transparency & Feature Toggles
    |--------------------------------------------------------------------------
    | Feature flags for frontend components and disclosure notices.
    */
    'features' => [
        'show_source_links'   => true,  // Show canonical source link on articles
        'show_editorial_note' => true,  // Show "Written with technological support" disclosure
    ],
];
