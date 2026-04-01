<?php

return [
    // Navegación
    'home'          => 'Inicio',
    'news'          => 'Noticias',
    'categories'    => 'Categorías',
    'tags'          => 'Etiquetas',
    'about'         => 'Acerca de',
    'contact'       => 'Contacto',
    'search'        => 'Buscar',
    'latest_news'   => 'Últimas Noticias',

    // Detalles del artículo y Tarjeta
    'read_more'     => 'Leer más',
    'min_read'      => ':count min de lectura',
    'published_by'  => 'Por :author',
    'published_on'  => 'Publicado el :date',
    'current_post'  => 'Post Actual',
    'views_count'   => ':count vistas',
    'share_post'    => 'Comparte este Post',
    'verified_author'=> 'Autor Verificado',
    'recommended'   => 'Recomendado',
    'featured'      => 'Destacado',
    'staff'         => 'Personal',
    'reporter'      => 'Reportero',

    // Página de Inicio y secciones
    'just_published'    => 'RECIÉN PUBLICADO',
    'browsing_category' => 'Explorando Categoría',
    'topic'             => 'Tema',
    'the_editorial'     => 'Editorial',
    'editorial_title'   => 'El Futuro de la Tecnología y la Innovación.',
    'editorial_subtitle'=> config('global.tagline') . ': Análisis profundos e información en tiempo real sobre las tendencias que cambian el mundo.',
    'archives_empty'    => 'El archivo está vacío',
    'expect_insights'   => 'Nuevos artículos muy pronto.',
    'trending_topics'   => 'Temas de Tendencia',

    // Newsletter
    'newsletter_title'  => config('global.site_name') . ' Weekly',
    'newsletter_desc'   => 'Recibe las actualizaciones tecnológicas más importantes directamente en tu bandeja de entrada. Sin rodeos, solo valor.',
    'email_address'     => 'Correo electrónico',
    'subscribe_now'     => 'Suscribirse Ahora',

    // Actualizaciones en vivo / Notificaciones
    'new_update'    => 'Nueva Actualización',
    'read_now'      => 'Leer Ahora',
    'dismiss'       => 'Cerrar',

    // Preferencias
    'theme'         => 'Tema',
    'language'      => 'Idioma',
    'preferences'   => 'Preferencias',

    // Selector de idioma
    'switch_lang'   => 'English',
    'current_lang'  => 'Español',

    // SEO por defecto
    'site_name'     => config('global.site_name', 'Glodaxia'),
    'meta_desc'     => 'Las últimas noticias de tecnología, innovación y tendencias globales, redactadas por expertos de ' . config('global.site_name') . '.',

    // E-E-A-T & Transparencia
    'read_original_source' => 'Leer fuente original',
];
