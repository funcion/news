<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site Metadata & Identity
    |--------------------------------------------------------------------------
    |
    | Global configuration values for the Glodaxia media platform.
    |
    */

    'site' => [
        'name' => 'Glodaxia',
        'tagline' => [
            'es' => 'Periodismo Tecnológico de Vanguardia',
            'en' => 'Next-Gen Technology Journalism',
        ],
        'description' => [
            'es' => 'Tu fuente diaria de noticias, análisis y tendencias sobre Inteligencia Artificial, desarrollo de software, ciberseguridad y tecnología global.',
            'en' => 'Your daily source for tech news, in-depth analysis, and emerging trends in AI, software development, cybersecurity, and global innovation.',
        ],
        'url' => env('APP_URL', 'https://glodaxia.com'),
        'contact_email' => env('MAIL_FROM_ADDRESS', 'hi@glodaxia.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Editorial Team & Authors
    |--------------------------------------------------------------------------
    |
    | 12 Human freelance tech writers with broad, open-topic personal bios.
    |
    */

    'editorial' => [
        'authors' => array (
  0 => 
  array (
    'name' => 'Luis Figuera',
    'slug' => 'luis-figuera',
    'email' => 'luis.figuera@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Luis. Me dedico a desarrollar aplicaciones web, móviles y de escritorio, trabajo como freelancer desde el año 2009, y me apasiona escribir y opinar sobre cómo la inteligencia artificial y el código moderno están transformando el software entre muchos otros temas.',
      'en' => 'Hi! I\'m Luis. I develop web, mobile, and desktop applications, working as a freelancer since 2009, and I love writing and sharing my views on how artificial intelligence and modern code are transforming software, among many other topics.',
    ),
    'voice_style' => 'técnico, cercano, enfocado en desarrollo y arquitectura moderna',
    'specialty' => 'development',
  ),
  1 => 
  array (
    'name' => 'Meudys Vásquez',
    'slug' => 'meudys-vasquez',
    'email' => 'meudys.vasquez@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Mi nombre es Meudys. Me dedico a la docencia y a la formación educativa, trabajo como redactora freelance, y me apasiona escribir y opinar sobre el impacto de las herramientas digitales, la IA en el aprendizaje y la innovación pedagógica entre muchos otros temas.',
      'en' => 'Hello! My name is Meudys. I work in education and teaching, collaborating as a freelance writer, and I love writing and sharing my views on digital tools, AI in learning, and educational innovation, among many other topics.',
    ),
    'voice_style' => 'didáctico, pedagógico, claro y conversacional',
    'specialty' => 'edtech',
  ),
  2 => 
  array (
    'name' => 'Leudys Vásquez',
    'slug' => 'leudys-vasquez',
    'email' => 'leudys.vasquez@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Leudys. Me dedico a la ingeniería industrial y a la optimización de procesos, trabajo como redactora freelance, y me apasiona escribir y opinar sobre automatización, productividad, gestión de proyectos y tecnología aplicada entre muchos otros temas.',
      'en' => 'Hi! I\'m Leudys. I work in industrial engineering and process optimization, writing as a freelancer, and I love writing and sharing my views on automation, productivity, project management, and applied technology, among many other topics.',
    ),
    'voice_style' => 'práctico, enfocado en productividad y automatización',
    'specialty' => 'automation',
  ),
  3 => 
  array (
    'name' => 'Wilmer Rivas',
    'slug' => 'wilmer-rivas',
    'email' => 'wilmer.rivas@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Wilmer. Me dedico a la ingeniería industrial y al análisis de datos, trabajo como redactor freelance, y me apasiona escribir y opinar sobre logística inteligente, analítica empresarial, cadena de suministro y avances tecnológicos entre muchos otros temas.',
      'en' => 'Hi! I\'m Wilmer. I work in industrial engineering and data analytics, writing as a freelancer, and I love writing and sharing my views on smart logistics, business analytics, supply chain, and technological breakthroughs, among many other topics.',
    ),
    'voice_style' => 'analítico, basado en datos y ejemplos reales',
    'specialty' => 'data_analytics',
  ),
  4 => 
  array (
    'name' => 'Mariaurys González',
    'slug' => 'mariaurys-gonzalez',
    'email' => 'mariaurys.gonzalez@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Mi nombre es Mariaurys. Me dedico a la administración de empresas y a la consultoría, trabajo como redactora freelance, y me apasiona escribir y opinar sobre modelos de negocio digitales, comercio electrónico, finanzas y estrategia corporativa entre muchos otros temas.',
      'en' => 'Hello! My name is Mariaurys. I work in business administration and consulting, writing as a freelancer, and I love writing and sharing my views on digital business models, e-commerce, finance, and corporate strategy, among many other topics.',
    ),
    'voice_style' => 'estratégico, claro, enfocado en negocios y dinero digital',
    'specialty' => 'business_management',
  ),
  5 => 
  array (
    'name' => 'Jesús Millán',
    'slug' => 'jesus-millan',
    'email' => 'jesus.millan@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Jesús. Me dedico a la investigación digital y a la creación de contenidos, trabajo como freelancer, y me apasiona escribir y opinar sobre tendencias de internet, gadgets, cultura digital, redes sociales y estilo de vida tecnológico entre muchos otros temas.',
      'en' => 'Hi! I\'m Jesús. I work in digital research and content creation as a freelancer, and I love writing and sharing my views on internet trends, gadgets, digital culture, social media, and tech lifestyle, among many other topics.',
    ),
    'voice_style' => 'fresco, dinámico, ágil y conversacional',
    'specialty' => 'trends_culture',
  ),
  6 => 
  array (
    'name' => 'Andrea García',
    'slug' => 'andrea-garcia',
    'email' => 'andrea.garcia@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Andrea. Me dedico a la ingeniería química y a la consultoría ambiental, trabajo como redactora freelance, y me apasiona escribir y opinar sobre nanotecnología, ciencia de materiales, biotecnología, sostenibilidad y energías limpias entre muchos otros temas.',
      'en' => 'Hello! I\'m Andrea. I work in chemical engineering and environmental consulting, writing as a freelancer, and I love writing and sharing my views on nanotechnology, material science, biotechnology, sustainability, and clean energy, among many other topics.',
    ),
    'voice_style' => 'curioso, científico, amigable y divulgativo',
    'specialty' => 'biotech_science',
  ),
  7 => 
  array (
    'name' => 'Ediliana Figuera',
    'slug' => 'ediliana-figuera',
    'email' => 'ediliana.figuera@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Ediliana. Me dedico a la medicina como médico cirujano, trabajo como redactora freelance, y me apasiona escribir y opinar sobre salud digital, biotecnología médica, diagnóstico asistido por IA y bienestar integral entre muchos otros temas.',
      'en' => 'Hi! I\'m Ediliana. I practice medicine as a general surgeon, writing as a freelancer, and I love writing and sharing my views on digital health, medical biotechnology, AI-assisted diagnostics, and holistic wellness, among many other topics.',
    ),
    'voice_style' => 'humano, empático, médico y preventivo',
    'specialty' => 'health_tech',
  ),
  8 => 
  array (
    'name' => 'Braniam Figuera',
    'slug' => 'braniam-figuera',
    'email' => 'braniam.figuera@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Mi nombre es Braniam. Me dedico a la práctica médica como médico cirujano, trabajo como redactor freelance, y me apasiona escribir y opinar sobre informática médica, dispositivos biomédicos de vanguardia, telemedicina y el futuro de la salud entre muchos otros temas.',
      'en' => 'Hello! My name is Braniam. I practice medicine as a general surgeon, writing as a freelancer, and I love writing and sharing my views on medical informatics, cutting-edge biomedical devices, telemedicine, and the future of healthcare, among many other topics.',
    ),
    'voice_style' => 'informativo, clínico, accesible y riguroso',
    'specialty' => 'medical_informatics',
  ),
  9 => 
  array (
    'name' => 'César Márquez',
    'slug' => 'cesar-marquez',
    'email' => 'cesar.marquez@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy César. Me dedico a la consultoría de innovación y al emprendimiento digital, trabajo como redactor freelance, y me apasiona escribir y opinar sobre el ecosistema de startups, herramientas no-code, plataformas de automatización y nuevos productos tech entre muchos otros temas.',
      'en' => 'Hi! I\'m César. I work in innovation consulting and digital entrepreneurship, writing as a freelancer, and I love writing and sharing my views on the startup ecosystem, no-code tools, automation platforms, and new tech products, among many other topics.',
    ),
    'voice_style' => 'entusiasta, emprendedor, no-code y práctico',
    'specialty' => 'startups',
  ),
  10 => 
  array (
    'name' => 'Roger Figuera',
    'slug' => 'roger-figuera',
    'email' => 'roger.figuera@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Roger. Me dedico al diseño gráfico y a la dirección de arte, trabajo como diseñador y redactor freelance, y me apasiona escribir y opinar sobre diseño de interfaces UI/UX, estética visual, tipografía, branding digital y experiencia de usuario entre muchos otros temas.',
      'en' => 'Hey! I\'m Roger. I work in graphic design and art direction, collaborating as a freelance designer and writer, and I love writing and sharing my views on UI/UX interface design, visual aesthetics, typography, digital branding, and user experience, among many other topics.',
    ),
    'voice_style' => 'creativo, visual, enfocado en experiencia de usuario',
    'specialty' => 'design_ui_ux',
  ),
  11 => 
  array (
    'name' => 'Robert Figuera',
    'slug' => 'robert-figuera',
    'email' => 'robert.figuera@glodaxia.com',
    'bio' => 
    array (
      'es' => '¡Hola! Soy Robert. Me dedico a la música profesional como saxofonista y productor, trabajo como redactor freelance, y me apasiona escribir y opinar sobre tecnología de audio digital, acústica, software de producción musical, sintetizadores e innovación sonora entre muchos otros temas.',
      'en' => 'Hi! I\'m Robert. I work in professional music as a saxophonist and producer, writing as a freelancer, and I love writing and sharing my views on digital audio technology, acoustics, music production software, synthesizers, and sonic innovation, among many other topics.',
    ),
    'voice_style' => 'apasionado, creativo, sonoro y musical',
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