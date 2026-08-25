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
    | 12 Diverse human freelance tech writers with authentic, creative voices.
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
      'es' => '¡Hola! Soy Luis. Llevo desde 2009 creando aplicaciones web, móviles y de escritorio como desarrollador independiente. Me fascina desmenuzar cómo el código moderno y la inteligencia artificial están revolucionando la forma en que construimos software hoy en día.',
      'en' => 'Hi! I\'m Luis. I\'ve been building web, mobile, and desktop apps as an independent developer since 2009. I\'m fascinated by breaking down how modern code and artificial intelligence are revolutionizing software development today.',
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
      'es' => '¡Qué tal! Soy Meudys. Vengo del mundo de la docencia y siempre he creído que la mejor tecnología es la que se entiende sin complicaciones. Escribo como redactora freelance para explorar cómo las herramientas digitales y la IA pueden hacer el aprendizaje mucho más inspirador y cercano.',
      'en' => 'Hey! I\'m Meudys. Coming from the world of teaching, I\'ve always believed the best technology is the kind that\'s easy to grasp. I write as a freelancer to explore how digital tools and AI can make learning much more engaging and inspiring.',
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
      'es' => '¡Hola a todos! Mi nombre es Leudys. Como ingeniera industrial, mi mente siempre busca simplificar flujos y eliminar cuellos de botella. Colaboro como redactora independiente compartiendo análisis sobre automatización inteligente, herramientas de productividad y soluciones que ahorran horas de trabajo.',
      'en' => 'Hello everyone! My name is Leudys. As an industrial engineer, my mind is wired to streamline workflows and remove bottlenecks. I write as a freelance analyst covering smart automation, productivity tools, and solutions that save hours of work.',
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
      'es' => '¡Un saludo! Soy Wilmer. Combino mi formación en ingeniería industrial con el análisis de datos para entender el pulso real de los mercados. Me encanta redactar artículos que conecten la logística, la cadena de suministro y los avances tecnológicos con historias cotidianas y comprensibles.',
      'en' => 'Greetings! I\'m Wilmer. I blend industrial engineering with data analytics to understand market dynamics. I love writing articles that connect logistics, supply chain tech, and industry breakthroughs with clear, relatable storytelling.',
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
      'es' => '¡Hola! Soy Mariaurys. Mi pasión es la administración y el crecimiento de negocios en la era digital. Como redactora freelance, me concentro en analizar cómo el comercio electrónico, las nuevas plataformas financieras y la innovación tecnológica transforman las empresas del mañana.',
      'en' => 'Hi! I\'m Mariaurys. My passion lies in business administration and digital-age growth. As a freelance writer, I focus on analyzing how e-commerce, new fintech platforms, and tech innovation are shaping tomorrow\'s businesses.',
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
      'es' => '¡Hola! Por aquí Jesús. Paso gran parte de mi tiempo rastreando novedades en internet, comunidades tech y cultura digital. Escribo de forma fresca y sin rodeos sobre esos gadgets curiosos, tendencias virales y noticias tecnológicas que realmente vale la pena conocer.',
      'en' => 'Hi there! Jesús here. I spend most of my days exploring internet trends, tech communities, and digital culture. I write in a fresh, candid style about interesting gadgets, viral trends, and tech news that are genuinely worth your time.',
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
      'es' => '¡Hola a todos! Soy Andrea. Desde la ingeniería química me apasiona descubrir lo que no se ve a simple vista: nanotecnología, nuevos materiales y biotecnología. Como redactora freelance, me entusiasma divulgar cómo la ciencia y la tecnología se unen para crear un futuro más sostenible.',
      'en' => 'Hey everyone! I\'m Andrea. As a chemical engineer, I love exploring what lies beyond the visible: nanotechnology, novel materials, and biotech. As a freelance writer, I\'m passionate about sharing how science and tech unite to build a greener future.',
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
      'es' => '¡Hola! Mi nombre es Ediliana. Ejerzo como médico cirujano y sigo con enorme entusiasmo la evolución de la salud digital. Escribo artículos independientes para explicar con rigor humano cómo los algoritmos diagnósticos, los avances biomédicos y la IA están mejorando la atención hospitalaria.',
      'en' => 'Hello! My name is Ediliana. I practice as a surgeon and closely follow the evolution of digital health. I write independent articles to explain, with human warmth and clinical rigor, how diagnostic algorithms, biomedical breakthroughs, and AI are enhancing patient care.',
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
      'es' => '¡Saludos! Soy Braniam. Vivir la medicina como cirujano me ha demostrado el impacto vital que tienen los instrumentos de precisión. Como redactor freelance, me gusta contar de primera mano hacia dónde van la telemedicina, los implantes biomédicos y las tecnologías que están salvando vidas a diario.',
      'en' => 'Greetings! I\'m Braniam. Practicing medicine as a surgeon has shown me the life-saving impact of precision tech. As a freelance writer, I love sharing first-hand insights on where telemedicine, biomedical devices, and cutting-edge healthcare tech are headed.',
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
      'es' => '¡Hola! Soy César. Me apasiona el universo de las startups y la ola del desarrollo no-code. Como redactor freelancer, disfruto analizando cómo creadores de todo el mundo están lanzando productos increíbles y automatizaciones complejas sin necesidad de escribir una sola línea de código.',
      'en' => 'Hi! I\'m César. I\'m passionate about the startup ecosystem and the rise of no-code platforms. As a freelance writer, I enjoy analyzing how creators worldwide launch incredible digital products and smart automations without writing a single line of code.',
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
      'es' => '¡Qué tal! Soy Roger. Veo la tecnología a través del prisma del diseño visual y la experiencia de usuario. Como redactor freelance, me encanta hablar sobre cómo la tipografía, las interfaces limpias y el buen diseño UI/UX convierten productos complejos en herramientas intuitivas y placenteras.',
      'en' => 'Hey there! I\'m Roger. I look at technology through the lens of visual design and user experience. As a freelance writer, I love discussing how typography, clean interfaces, and thoughtful UI/UX turn complex tools into intuitive, delightful products.',
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
      'es' => '¡Hola a todos! Soy Robert. Como saxofonista y apasionado del sonido, vivo explorando la frontera donde la música se encuentra con la tecnología. Escribo como freelancer sobre sintetizadores, software de producción musical, acústica digital y cómo los algoritmos modernos están reinventando el arte sonoro.',
      'en' => 'Hi everyone! I\'m Robert. As a saxophonist and sound enthusiast, I live at the intersection of music and technology. I write as a freelancer about synths, music production software, digital acoustics, and how modern algorithms are reinventing the sonic arts.',
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