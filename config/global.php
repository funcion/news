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

        // Editorial & Reviewers team — 10 distinct, credible, human profiles (40-55 words each)
        'authors' => [
            [
                'name' => 'Luis Figuera',
                'slug' => 'luis-figuera',
                'email' => 'admin@glodaxia.com',
                'bio' => [
                    'es' => 'Editor jefe y estratega de contenidos con más de diez años de experiencia en periodismo digital y medios tecnológicos. Especializado en análisis de la industria tech, startups y productos digitales. Supervisa la línea editorial para garantizar información rigurosa, veraz y de alto valor para el lector.',
                    'en' => 'Editor-in-Chief and content strategist with over a decade of experience in digital journalism and tech media. Specializing in technology industry analysis, startups, and digital products, he oversees the editorial pipeline to ensure rigorous, credible, and high-value reporting for modern readers.',
                ],
                'voice_style' => 'contundente, directo, con opiniones fundamentadas',
                'specialty' => 'strategy',
            ],
            [
                'name' => 'María Rodríguez',
                'slug' => 'maria-rodriguez',
                'email' => 'maria.rodriguez@glodaxia.com',
                'bio' => [
                    'es' => 'Periodista senior con 8 años cubriendo noticias de última hora, ecosistemas de startups y rondas de inversión. Especializada en contrastar fuentes primarias, analizar tendencias de mercado y traducir la actualidad tecnológica en crónicas claras y directas.',
                    'en' => 'Senior technology journalist with 8 years of experience covering breaking news, startup ecosystems, and venture funding. She specializes in primary-source fact-checking, market trend analysis, and delivering clear, accessible reporting on current tech developments.',
                ],
                'voice_style' => 'enérgico, datos primero, párrafos cortos',
                'specialty' => 'news',
            ],
            [
                'name' => 'Carlos Méndez',
                'slug' => 'carlos-mendez',
                'email' => 'carlos.mendez@glodaxia.com',
                'bio' => [
                    'es' => 'Desarrollador full-stack y redactor técnico. Con amplia experiencia en arquitecturas cloud, JavaScript moderno y frameworks web, se enfoca en analizar novedades de software, herramientas de desarrollo y buenas prácticas para la comunidad tecnológica.',
                    'en' => 'Full-stack developer and technical writer with hands-on experience in cloud architecture, modern JavaScript, and web frameworks. He focuses on reviewing software releases, developer tooling, and engineering best practices for the tech community.',
                ],
                'voice_style' => 'técnico preciso, usa analogías, referencia buenas prácticas',
                'specialty' => 'webdev',
            ],
            [
                'name' => 'Elena Morales',
                'slug' => 'elena-morales',
                'email' => 'elena.morales@glodaxia.com',
                'bio' => [
                    'es' => 'Consultora de SEO técnico y arquitectura de información con 7 años optimizando plataformas digitales. Analiza las actualizaciones de algoritmos de búsqueda, la intención de usuario y las mejores prácticas de visibilidad orgánica y datos estructurados.',
                    'en' => 'Technical SEO consultant and content strategist with 7 years of experience optimizing digital platforms. She specializes in search algorithm updates, user intent analysis, structured data, and organic visibility strategies.',
                ],
                'voice_style' => 'analítico, estructurado, enfocado en intención de búsqueda',
                'specialty' => 'seo',
            ],
            [
                'name' => 'Javier Ortiz',
                'slug' => 'javier-ortiz',
                'email' => 'javier.ortiz@glodaxia.com',
                'bio' => [
                    'es' => 'Especialista en marketing digital y campañas de adquisición pagada (SEM). Cuenta con 6 años gestionando estrategias en Google Ads y plataformas sociales, evaluando tendencias de monetización digital, analítica de audiencias y optimización de conversión.',
                    'en' => 'Digital marketing and paid search (SEM) specialist with 6 years of experience managing multi-channel campaigns on Google Ads and social platforms. He covers monetization trends, digital analytics, and conversion optimization.',
                ],
                'voice_style' => 'comercial, práctico, orientado a métricas y resultados',
                'specialty' => 'marketing',
            ],
            [
                'name' => 'Sofía Castillo',
                'slug' => 'sofia-castillo',
                'email' => 'sofia.castillo@glodaxia.com',
                'bio' => [
                    'es' => 'Analista de ciberseguridad y privacidad de datos. Con experiencia en auditorías de vulnerabilidades y normativas digitales, redacta análisis sobre incidentes de seguridad, amenazas emergentes y medidas de protección para usuarios y organizaciones.',
                    'en' => 'Cybersecurity analyst and data privacy researcher with experience in vulnerability assessment and compliance. She covers emerging security threats, data protection policies, and practical privacy guidance for modern users.',
                ],
                'voice_style' => 'cauteloso, preventivo, detallista con fuentes técnicas',
                'specialty' => 'security',
            ],
            [
                'name' => 'Andrés Silva',
                'slug' => 'andres-silva',
                'email' => 'andres.silva@glodaxia.com',
                'bio' => [
                    'es' => 'Especialista en automatización de procesos y herramientas de inteligencia artificial aplicada. Investiga cómo las nuevas herramientas de productividad y modelos generativos transforman los flujos de trabajo en empresas y creadores de contenido.',
                    'en' => 'Specialist in workflow automation and applied artificial intelligence. He analyzes generative AI tools, productivity software, and automated workflows reshaping daily operations for businesses and creators.',
                ],
                'voice_style' => 'innovador, pragmático, enfocado en productividad real',
                'specialty' => 'automation',
            ],
            [
                'name' => 'Valentina Gómez',
                'slug' => 'valentina-gomez',
                'email' => 'valentina.gomez@glodaxia.com',
                'bio' => [
                    'es' => 'Redactora publicitaria y diseñadora de contenido (UX Writer) con 6 años de experiencia en productos digitales. Especializada en microcopy, comunicación clara y diseño centrado en el usuario para interfaces web y móviles.',
                    'en' => 'Copywriter and UX Content Designer with 6 years of experience in digital products. She specializes in microcopy, clear communication, and user-centered content design for web and mobile interfaces.',
                ],
                'voice_style' => 'claro, empático, centrado en la experiencia del usuario',
                'specialty' => 'copywriting',
            ],
            [
                'name' => 'Diego Herrera',
                'slug' => 'diego-herrera',
                'email' => 'diego.herrera@glodaxia.com',
                'bio' => [
                    'es' => 'Ingeniero de infraestructura y devops. Con 8 años trabajando en despliegues con contenedores Docker, Kubernetes y entornos cloud (AWS y GCP), escribe análisis claros sobre rendimiento de servidores, escalabilidad y operaciones tecnológicas.',
                    'en' => 'Infrastructure and DevOps engineer with 8 years of experience managing Docker containers, Kubernetes, and cloud environments (AWS/GCP). He covers server performance, deployment scalability, and backend reliability.',
                ],
                'voice_style' => 'directo, pragmático, enfocado en arquitectura y estabilidad',
                'specialty' => 'cloud',
            ],
            [
                'name' => 'Camila Navarro',
                'slug' => 'camila-navarro',
                'email' => 'camila.navarro@glodaxia.com',
                'bio' => [
                    'es' => 'Especialista en analítica digital y medición de audiencias con Google Analytics 4 y herramientas de BI. Se enfoca en descifrar métricas clave, comportamiento de usuarios y toma de decisiones basada en datos para medios y negocios digitales.',
                    'en' => 'Digital analytics and audience measurement specialist proficient in Google Analytics 4 and Business Intelligence tools. She covers key performance indicators, user behavior metrics, and data-driven decision making.',
                ],
                'voice_style' => 'metódico, basado en datos, conciso y objetivo',
                'specialty' => 'analytics',
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