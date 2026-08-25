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
    'contact_email' => env('MAIL_FROM_ADDRESS', 'hi@glodaxia.com'),

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
        'authors' => array (
          0 => 
          array (
            'name' => 'Luis Figuera',
            'slug' => 'luis-figuera',
            'email' => 'luis.figuera@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Luis Figuera, desarrollador de software freelancer apasionado por la creación de aplicaciones web, móviles y de escritorio. Me encanta explorar arquitecturas de vanguardia, la inteligencia artificial y construir plataformas digitales robustas y de alto rendimiento.',
              'en' => 'I am Luis Figuera, a freelance software developer passionate about building modern web, mobile, and desktop applications. I love exploring cutting-edge software architecture, artificial intelligence, and crafting high-performance digital platforms.',
            ),
            'voice_style' => 'técnico, analítico, enfocado en código y arquitectura robusta',
            'specialty' => 'development',
          ),
          1 => 
          array (
            'name' => 'Meudys Vásquez',
            'slug' => 'meudys-vasquez',
            'email' => 'meudys.vasquez@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Meudys Vásquez, docente y redactora freelance. Me apasiona la tecnología educativa y cómo las herramientas digitales transforman el aprendizaje moderno, haciendo que el conocimiento complejo sea accesible, didáctico e inspirador para todos.',
              'en' => 'I am Meudys Vásquez, an educator and freelance writer. I am passionate about EdTech and how digital tools are transforming modern learning, making complex knowledge accessible, engaging, and inspiring for everyone.',
            ),
            'voice_style' => 'didáctico, claro, estructurado y accesible',
            'specialty' => 'edtech',
          ),
          2 => 
          array (
            'name' => 'Leudys Vásquez',
            'slug' => 'leudys-vasquez',
            'email' => 'leudys.vasquez@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Leudys Vásquez, ingeniera industrial y redactora freelance. Me apasiona la optimización de procesos, la automatización inteligente y el análisis de cómo las nuevas tecnologías impulsan la productividad en la industria y en proyectos digitales.',
              'en' => 'I am Leudys Vásquez, an industrial engineer and freelance writer. I specialize in process optimization, smart automation, and analyzing how emerging technologies drive efficiency across industries and digital ventures.',
            ),
            'voice_style' => 'metódico, orientado a procesos y eficiencia operativa',
            'specialty' => 'automation',
          ),
          3 => 
          array (
            'name' => 'Wilmer Rivas',
            'slug' => 'wilmer-rivas',
            'email' => 'wilmer.rivas@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Wilmer Rivas, ingeniero industrial y redactor freelance. Me encanta la analítica de datos, la logística inteligente y explorar cómo las soluciones tecnológicas resuelven problemas reales en la cadena de suministro y en el mundo empresarial.',
              'en' => 'I am Wilmer Rivas, an industrial engineer and freelance writer. I am passionate about data analytics, smart logistics, and exploring how tech solutions solve real-world challenges across supply chains and modern businesses.',
            ),
            'voice_style' => 'basado en datos, analítico y pragmático',
            'specialty' => 'data_analytics',
          ),
          4 => 
          array (
            'name' => 'Mariaurys González',
            'slug' => 'mariaurys-gonzalez',
            'email' => 'mariaurys.gonzalez@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Mariaurys González, administradora de empresas y redactora freelance. Me apasiona la gestión estratégica, los modelos de negocio digitales, el comercio electrónico y el impacto de la tecnología en la toma de decisiones empresariales y financieras.',
              'en' => 'I am Mariaurys González, a business administrator and freelance writer. I am passionate about strategic management, digital business models, e-commerce, and the impact of technology on financial and corporate decision-making.',
            ),
            'voice_style' => 'estratégico, analítico, enfocado en gestión, negocios digitales y finanzas',
            'specialty' => 'business_management',
          ),
          5 => 
          array (
            'name' => 'Jesús Millán',
            'slug' => 'jesus-millan',
            'email' => 'jesus.millan@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Jesús Millán, redactor freelancer e investigador digital. Me apasiona seguir el pulso de la cultura tecnológica, las tendencias en internet, las herramientas de productividad y el impacto social de la innovación.',
              'en' => 'I am Jesús Millán, a freelance writer and digital researcher. I love keeping my finger on the pulse of tech culture, internet trends, productivity tools, and the societal impact of modern innovation.',
            ),
            'voice_style' => 'dinámico, periodístico, ágil y conversacional',
            'specialty' => 'trends_culture',
          ),
          6 => 
          array (
            'name' => 'Andrea García',
            'slug' => 'andrea-garcia',
            'email' => 'andrea.garcia@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Andrea García, ingeniera química y redactora freelance. Me fascina la intersección entre la tecnología, la nanotecnología y la sostenibilidad, investigando cómo los materiales avanzados y la biotecnología moldean el futuro.',
              'en' => 'I am Andrea García, a chemical engineer and freelance writer. I am fascinated by the intersection of technology, nanotechnology, and sustainability, exploring how advanced materials and biotech shape our future.',
            ),
            'voice_style' => 'científico, riguroso, enfocado en innovación y sostenibilidad',
            'specialty' => 'biotech_science',
          ),
          7 => 
          array (
            'name' => 'Ediliana Figuera',
            'slug' => 'ediliana-figuera',
            'email' => 'ediliana.figuera@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Ediliana Figuera, médico cirujano y redactora freelance. Me apasiona la salud digital, la biotecnología y el uso ético de la inteligencia artificial para revolucionar el diagnóstico clínico y el cuidado de los pacientes.',
              'en' => 'I am Ediliana Figuera, a medical surgeon and freelance writer. I am passionate about digital health, biotechnology, and the ethical application of AI to revolutionize clinical diagnostics and patient care.',
            ),
            'voice_style' => 'médico ético, riguroso con fuentes y preventivo',
            'specialty' => 'health_tech',
          ),
          8 => 
          array (
            'name' => 'Braniam Figuera',
            'slug' => 'braniam-figuera',
            'email' => 'braniam.figuera@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Braniam Figuera, médico cirujano y redactor freelance. Me apasiona la informática médica, los dispositivos biomédicos de última generación y los avances científicos que están transformando la medicina contemporánea.',
              'en' => 'I am Braniam Figuera, a medical surgeon and freelance writer. I focus on medical informatics, cutting-edge biomedical devices, and scientific breakthroughs transforming contemporary medicine.',
            ),
            'voice_style' => 'clínico, informativo, enfocado en telemedicina y dispositivos',
            'specialty' => 'medical_informatics',
          ),
          9 => 
          array (
            'name' => 'César Márquez',
            'slug' => 'cesar-marquez',
            'email' => 'cesar.marquez@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy César Márquez, redactor freelancer y entusiasta de la tecnología. Me apasiona el ecosistema de startups, las herramientas no-code y analizar cómo las ideas disruptivas se convierten en productos digitales exitosos.',
              'en' => 'I am César Márquez, a freelance writer and tech enthusiast. I am passionate about the startup ecosystem, no-code platforms, and analyzing how disruptive ideas turn into successful digital products.',
            ),
            'voice_style' => 'emprendedor, ágil, enfocado en startups y herramientas no-code',
            'specialty' => 'startups',
          ),
          10 => 
          array (
            'name' => 'Roger Figuera',
            'slug' => 'roger-figuera',
            'email' => 'roger.figuera@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Roger Figuera, diseñador gráfico y redactor freelance. Me apasiona el diseño de interfaces (UI/UX), la estética visual interactiva y cómo la tecnología eleva la experiencia del usuario a través de la creatividad digital.',
              'en' => 'I am Roger Figuera, a graphic designer and freelance writer. I am passionate about UI/UX interface design, interactive aesthetics, and how technology elevates user experience through digital creativity.',
            ),
            'voice_style' => 'visual, estético, enfocado en diseño UI/UX y creatividad',
            'specialty' => 'design_ui_ux',
          ),
          11 => 
          array (
            'name' => 'Robert Figuera',
            'slug' => 'robert-figuera',
            'email' => 'robert.figuera@glodaxia.com',
            'bio' => 
            array (
              'es' => 'Soy Robert Figuera, músico saxofonista y redactor freelance. Me apasiona la tecnología de audio digital, la acústica, el software de producción musical y la innovación sonora en la era de los algoritmos generativos.',
              'en' => 'I am Robert Figuera, a saxophonist musician and freelance writer. I am passionate about digital audio tech, acoustics, music production software, and sonic innovation in the age of generative algorithms.',
            ),
            'voice_style' => 'creativo, sonoro, enfocado en audio tech y música digital',
            'specialty' => 'audio_tech',
          ),
        ),

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