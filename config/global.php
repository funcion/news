<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Glodaxia Branding & Identity
    |--------------------------------------------------------------------------
    |
    | Global settings for the platform branding.
    |
    */

    'site_name' => 'Glodaxia',
    'tagline'   => 'Tech & News Magazine',
    'footer_text' => 'Glodaxia Digital Media',

    /*
    |--------------------------------------------------------------------------
    | Editorial & AI Settings
    |--------------------------------------------------------------------------
    |
    | Rules for content generation, word counts, and AI persona.
    |
    */

    'editorial' => [
        // Persona for the AI when redact articles
        'persona' => 'world-class Senior Technology Journalist and elite SEO copywriter (15+ years experience) working for Glodaxia, a premium tech publication.',
        
        // Strict focus for the AI
        'focus_rules' => 'STRICTLY ADHERE TO THE FACTS PROVIDED. NEVER invent names, dates, statistics, or events not present in the SOURCE FACTS.',

        // Word count targets per content type — WIDE ranges for natural variation
        // A 300-word news brief and a 1200-word news analysis should both be valid
        'word_targets' => [
            'news'   => '300-1200 words EN | 300-1200 palabras ES',
            'blog'   => '600-1600 words EN | 600-1600 palabras ES',
            'guide'  => '1000-2500 words EN | 1000-2500 palabras ES',
            'review' => '600-1400 words EN | 600-1400 palabras ES',
            'pillar' => '1500-3000 words EN | 1500-3000 palabras ES',
        ],

        // Editorial team — each author has a distinct voice and specialty
        'authors' => [
            [
                'name' => 'Luis Figuera',
                'slug' => 'luis-figuera',
                'bio' => [
                    'en' => 'Editor and lead analyst at Glodaxia. Specializes in AI industry analysis, startup ecosystems, and the intersection of technology and society. 15+ years covering tech.',
                    'es' => 'Editor y analista principal en Glodaxia. Especialista en análisis de la industria IA, ecosistemas de startups y la intersección de tecnología y sociedad. 15+ años cubriendo tecnología.',
                ],
                'voice_style' => 'contundente, directo, con opiniones fuertes',
                'specialty' => 'analysis',
            ],
            [
                'name' => 'María Rodríguez',
                'slug' => 'maria-rodriguez',
                'bio' => [
                    'en' => 'Senior reporter at Glodaxia covering breaking tech news, product launches, and startup funding. 8 years in Silicon Valley journalism.',
                    'es' => 'Reportera senior en Glodaxia cubriendo noticias de última hora de tecnología, lanzamientos de productos y financiación de startups. 8 años en periodismo de Silicon Valley.',
                ],
                'voice_style' => 'enérgico, datos primero, párrafos cortos',
                'specialty' => 'news',
            ],
            [
                'name' => 'Carlos Méndez',
                'slug' => 'carlos-mendez',
                'bio' => [
                    'en' => 'Technical analyst at Glodaxia. PhD in Computer Science, former ML researcher. Specializes in breaking down complex papers and benchmarks for a general audience.',
                    'es' => 'Analista técnico en Glodaxia. PhD en Ciencias de la Computación, ex-investigador de ML. Especialista en explicar papers y benchmarks complejos para audiencia general.',
                ],
                'voice_style' => 'técnico preciso, usa analogías, referencia papers',
                'specialty' => 'guide',
            ],
        ],

        // Default author if no match found
        'default_author' => [
            'name' => 'Luis Figuera',
            'slug' => 'luis-figuera',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting — Editorial Cadence
    |--------------------------------------------------------------------------
    |
    | Controls how many articles can be published per day/hour.
    | Prevents patterns that search engines could flag as automated.
    |
    */

    'rate_limits' => [
        'max_articles_per_day' => (int) env('MAX_ARTICLES_PER_DAY', 8),
        'max_articles_per_hour' => (int) env('MAX_ARTICLES_PER_HOUR', 2),
        'max_articles_per_category_per_day' => 3,
        'min_hours_between_similar_topics' => 4,
        'publishing_hours' => [
            'start' => (int) env('PUBLISH_HOUR_START', 7),  // 7 AM
            'end'   => (int) env('PUBLISH_HOUR_END', 22),   // 10 PM
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transparency & Features
    |--------------------------------------------------------------------------
    |
    | Feature flags for the frontend components.
    |
    */

    'features' => [
        'show_source_links' => true,  // Show "Read original source" link on articles
        'show_editorial_note' => true, // Show "written with technological support" note
    ],
];
