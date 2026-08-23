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
        'word_targets' => [
            'news'   => '600-1200 words EN | 600-1200 palabras ES',
            'blog'   => '800-1600 words EN | 800-1600 palabras ES',
            'guide'  => '1250-2500 words EN | 1250-2500 palabras ES',
            'review' => '700-1400 words EN | 700-1400 palabras ES',
            'pillar' => '1500-3000 words EN | 1500-3000 palabras ES',
        ],

        // Editorial team — concise, credible human bios (40-55 words)
        'authors' => [
            [
                'name' => 'Luis Figuera',
                'slug' => 'luis-figuera',
                'bio' => [
                    'es' => 'Editor jefe y estratega de contenidos con más de diez años de experiencia en periodismo digital y medios tecnológicos. Especializado en análisis de la industria tech, startups y productos digitales. Supervisa la línea editorial para garantizar información rigurosa, veraz y de alto valor para el lector.',
                    'en' => 'Editor-in-Chief and content strategist with over a decade of experience in digital journalism and tech media. Specializing in technology industry analysis, startups, and digital products, he oversees the editorial pipeline to ensure rigorous, credible, and high-value reporting for modern readers.',
                ],
                'voice_style' => 'contundente, directo, con opiniones fundamentadas',
                'specialty' => 'analysis',
            ],
            [
                'name' => 'María Rodríguez',
                'slug' => 'maria-rodriguez',
                'bio' => [
                    'es' => 'Periodista senior con 8 años cubriendo noticias de última hora, ciberseguridad y capital de riesgo. Especializada en contrastar fuentes primarias, analizar tendencias de mercado y traducir la actualidad tecnológica en crónicas claras y directas.',
                    'en' => 'Senior technology journalist with 8 years of experience covering breaking news, cybersecurity, and venture capital. She specializes in primary-source fact-checking, market trend analysis, and delivering clear, accessible reporting on current tech developments.',
                ],
                'voice_style' => 'enérgico, datos primero, párrafos cortos',
                'specialty' => 'news',
            ],
            [
                'name' => 'Carlos Méndez',
                'slug' => 'carlos-mendez',
                'bio' => [
                    'es' => 'Analista técnico e investigador de software. Doctor en Ciencias de la Computación, se especializa en infraestructura cloud, desarrollo web y modelos de lenguaje aplicados, evaluando avances de ingeniería y traduciéndolos en análisis prácticos para desarrolladores y líderes tecnológicos.',
                    'en' => 'Technical analyst and software researcher with a Ph.D. in Computer Science. He specializes in cloud infrastructure, web engineering, and applied machine learning, evaluating technical breakthroughs into practical insights for developers and tech leaders.',
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