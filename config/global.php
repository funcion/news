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
    | 12 Human freelance tech journalists with warm, conversational bios.
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
      'es' => '¡Hola! Soy Luis. De día desarrollo aplicaciones web, móviles y de escritorio como freelancer, y por las noches me apasiona escribir y desglosar cómo la inteligencia artificial y el código moderno están transformando el mundo del software.',
      'en' => 'Hi! I\'m Luis. By day I build web, mobile, and desktop apps as a freelancer, and by night I love writing and breaking down how artificial intelligence and modern code are reshaping the tech world.',
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
      'es' => '¡Hola! Mi nombre es Meudys. Soy docente de profesión y redactora por vocación. Me encanta investigar cómo las herramientas digitales y la IA pueden hacer que el aprendizaje sea más fácil, entretenido y accesible para todos.',
      'en' => 'Hello! My name is Meudys. I\'m a teacher by profession and a writer at heart. I love exploring how digital tools and AI can make learning easier, more engaging, and accessible to everyone.',
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
      'es' => '¡Hola a todos! Soy Leudys. Como ingeniera industrial, vivo obsesionada con optimizar procesos y hacer las cosas más eficientes. Escribo como freelancer sobre automatización, productividad y las herramientas que nos ahorran tiempo en el trabajo.',
      'en' => 'Hey everyone! I\'m Leudys. As an industrial engineer, I\'m obsessed with optimizing workflows and efficiency. I write as a freelancer about smart automation, productivity hacks, and the tools that save us time at work.',
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
      'es' => '¡Hola! Soy Wilmer. Trabajo como ingeniero industrial y redactor digital. Me apasiona analizar datos, entender la logística detrás de las grandes empresas tech y contar historias claras sobre hacia dónde se mueve la industria.',
      'en' => 'Hi! I\'m Wilmer. I work as an industrial engineer and digital writer. I\'m passionate about analyzing data, understanding tech supply chains, and telling clear stories about where the industry is heading.',
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
      'es' => '¡Hola! Mi nombre es Mariaurys. Soy administradora de empresas y me fascina el mundo digital. En mi tiempo libre como redactora freelance, analizo cómo las nuevas tecnologías impulsan los negocios, el comercio online y las finanzas del día a día.',
      'en' => 'Hello! My name is Mariaurys. I\'m a business administrator fascinated by the digital economy. In my freelance writing, I cover how emerging tech drives modern business, e-commerce, and everyday finance.',
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
      'es' => '¡Hola! Soy Jesús. Trabajo como freelancer navegando a diario por internet, cazando las últimas tendencias, gadgets curiosos y novedades que marcan la cultura digital para contártelas de forma sencilla y directa.',
      'en' => 'Hi! I\'m Jesús. As a freelancer, I spend my days scouring the web for viral tech trends, exciting gadgets, and digital culture stories to share them with you in plain English.',
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
      'es' => '¡Hola a todos! Soy Andrea. Soy ingeniera química y redactora independiente. Me entusiasma descubrir cómo la ciencia de materiales, la nanotecnología y la biotecnología se combinan con la tecnología para crear un planeta más limpio y sostenible.',
      'en' => 'Hey there! I\'m Andrea. I\'m a chemical engineer and freelance writer. I get excited about how material science, nanotech, and biotech combine with computing to create a greener, more sustainable planet.',
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
      'es' => '¡Hola! Soy Ediliana. Soy médico cirujano y en mis ratos libres escribo sobre salud digital. Me apasiona explicar cómo los algoritmos de IA y la biotecnología están ayudando a salvar vidas y mejorar la atención médica en los hospitales.',
      'en' => 'Hi! I\'m Ediliana. I\'m a medical surgeon who loves writing about digital health. I\'m passionate about explaining how AI algorithms and biotech are helping doctors save lives and improve patient care.',
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
      'es' => '¡Hola! Mi nombre es Braniam. Como médico cirujano, me encanta la tecnología médica. Escribo como freelancer sobre los nuevos dispositivos biomédicos, telemedicina y los inventos científicos que están cambiando el futuro de la medicina.',
      'en' => 'Hello! My name is Braniam. As a surgeon, I\'m deeply passionate about medical tech. I write as a freelancer about breakthrough biomedical devices, telemedicine, and the inventions shaping the future of healthcare.',
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
      'es' => '¡Hola! Soy César. Trabajo como redactor freelancer y me encanta el mundo de las startups. Sigo de cerca a los emprendedores que crean aplicaciones increíbles sin saber programar gracias a las herramientas no-code y a la IA.',
      'en' => 'Hi! I\'m César. I work as a freelance writer with a huge passion for startups. I closely follow entrepreneurs building amazing apps without coding using no-code platforms and AI tools.',
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
      'es' => '¡Hola! Soy Roger. Soy diseñador gráfico y redactor freelance. Mi misión es analizar cómo el buen diseño UI/UX y la estética visual hacen que usar una app o una web sea una experiencia agradable e intuitiva para cualquier persona.',
      'en' => 'Hey! I\'m Roger. I\'m a graphic designer and freelance writer. My mission is to show how great UI/UX design and visual aesthetics make digital products intuitive and enjoyable for everyday users.',
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
      'es' => '¡Hola a todos! Soy Robert. Soy músico saxofonista y redactor freelance. Me apasiona todo lo que suena: sintetizadores, software de producción musical, plugins de audio y cómo la tecnología está redefiniendo la música moderna.',
      'en' => 'Hi everyone! I\'m Robert. I\'m a saxophonist and freelance writer. I\'m in love with audio tech: synths, DAWs, sound engineering plugins, and how digital tools are redefining modern music production.',
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