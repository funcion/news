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
    | 12 Human freelance tech writers driven by pure passion and expertise.
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
      'es' => '¡Hola! Soy Luis. Desarrollo aplicaciones web, móviles y de escritorio como freelancer, y por pura pasión escribo sobre cómo la inteligencia artificial y el código moderno están transformando el software.',
      'en' => 'Hi! I\'m Luis. I build web, mobile, and desktop apps as a freelancer, and out of pure passion I write about how artificial intelligence and modern code are reshaping software.',
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
      'es' => '¡Hola! Mi nombre es Meudys. Soy docente y redactora freelance. Me apasiona investigar y escribir sobre cómo las herramientas digitales y la IA hacen que aprender sea más fácil, entretenido y accesible.',
      'en' => 'Hello! My name is Meudys. I\'m an educator and freelance writer. I love exploring and writing about how digital tools and AI make learning easier, engaging, and accessible.',
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
      'es' => '¡Hola! Soy Leudys. Soy ingeniera industrial y me encanta la eficiencia. Escribo como freelancer por puro gusto sobre automatización de procesos, productividad y las tecnologías que nos hacen la vida más fácil.',
      'en' => 'Hey! I\'m Leudys. I\'m an industrial engineer who loves efficiency. I write as a freelancer out of passion for workflow automation, productivity, and tech that makes life easier.',
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
      'es' => '¡Hola! Soy Wilmer. Soy ingeniero industrial y redactor freelance apasionado por los datos, la logística inteligente y contar de forma clara cómo la tecnología está cambiando los negocios.',
      'en' => 'Hi! I\'m Wilmer. I\'m an industrial engineer and freelance writer passionate about data, smart logistics, and clearly explaining how technology is transforming business.',
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
      'es' => '¡Hola! Mi nombre es Mariaurys. Soy administradora de empresas y me fascina el mundo digital. Escribo como freelancer por pasión sobre innovación en los negocios, comercio electrónico y finanzas tech.',
      'en' => 'Hello! My name is Mariaurys. I\'m a business administrator fascinated by the digital world. I write as a freelancer out of passion for business innovation, e-commerce, and fintech.',
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
      'es' => '¡Hola! Soy Jesús. Soy redactor freelance y un apasionado de la tecnología. Me encanta descubrir las últimas tendencias en internet, gadgets curiosos y la cultura digital para compartirlas contigo.',
      'en' => 'Hi! I\'m Jesús. I\'m a freelance writer and tech lover. I enjoy tracking down the latest web trends, exciting gadgets, and digital culture stories to share with you.',
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
      'es' => '¡Hola! Soy Andrea. Soy ingeniera química y redactora freelance. Por pura pasión escribo sobre ciencia de materiales, nanotecnología, biotecnología y cómo la innovación ayuda a cuidar el medio ambiente.',
      'en' => 'Hello! I\'m Andrea. I\'m a chemical engineer and freelance writer. Out of pure passion, I write about material science, nanotechnology, biotech, and how innovation helps protect our planet.',
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
      'es' => '¡Hola! Soy Ediliana. Soy médico cirujano y una apasionada de la salud digital. Escribo como freelancer por vocación para explicar cómo la inteligencia artificial y la biotecnología mejoran la medicina.',
      'en' => 'Hi! I\'m Ediliana. I\'m a medical surgeon and a digital health enthusiast. I write as a freelancer out of passion to explain how artificial intelligence and biotech improve healthcare.',
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
      'es' => '¡Hola! Mi nombre es Braniam. Soy médico cirujano y me encanta la tecnología médica. Escribo como freelancer por gusto sobre dispositivos biomédicos avanzados, telemedicina y el futuro de la salud.',
      'en' => 'Hello! My name is Braniam. I\'m a medical surgeon and medical tech lover. I write as a freelancer about cutting-edge biomedical devices, telemedicine, and the future of health.',
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
      'es' => '¡Hola! Soy César. Soy redactor freelance y me entusiasma el ecosistema de startups. Me encanta analizar cómo nacen las nuevas ideas digitales y cómo cualquiera puede crear apps con herramientas no-code e IA.',
      'en' => 'Hi! I\'m César. I\'m a freelance writer enthusiastic about the startup ecosystem. I love analyzing how digital ideas are born and how anyone can build apps with no-code tools and AI.',
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
      'es' => '¡Hola! Soy Roger. Soy diseñador gráfico y redactor freelance. Por pasión por el diseño UI/UX y la estética visual, escribo sobre cómo crear experiencias digitales atractivas e intuitivas.',
      'en' => 'Hey! I\'m Roger. I\'m a graphic designer and freelance writer. Driven by a passion for UI/UX and visual aesthetics, I write about creating engaging and intuitive digital experiences.',
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
      'es' => '¡Hola! Soy Robert. Soy músico saxofonista y redactor freelance. Me apasiona el audio digital, los sintetizadores, el software de producción musical y explorar cómo la tecnología revoluciona el sonido.',
      'en' => 'Hi! I\'m Robert. I\'m a saxophonist and freelance writer. I\'m passionate about digital audio, synths, music production software, and exploring how tech is revolutionizing sound.',
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