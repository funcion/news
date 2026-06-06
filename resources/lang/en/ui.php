<?php

return [
    // Navigation
    'home'          => 'Home',
    'news'          => 'News',
    'categories'    => 'Categories',
    'tags'          => 'Tags',
    'about'         => 'About Us',
    'contact'       => 'Contact',
    'search'        => 'Search',
    'latest_news'   => 'Latest News',

    // Article Details & Card
    'read_more'     => 'Read More',
    'min_read'      => ':count min read',
    'published_by'  => 'By :author',
    'published_on'  => 'Published on :date',
    'current_post'  => 'Current Post',
    'views_count'   => ':count views',
    'share_post'    => 'Share This Post',
    'verified_author'=> 'Verified Author',
    'recommended'   => 'Recommended',
    'featured'      => 'Featured',
    'staff'         => 'Staff',
    'reporter'      => 'Reporter',

    // Home Page sections
    'just_published'    => 'JUST PUBLISHED',
    'browsing_category' => 'Browsing Category',
    'topic'             => 'Topic',
    'the_editorial'     => 'The Editorial',
    'editorial_title'   => 'The Future of Tech & Innovation.',
    'editorial_subtitle'=> config('global.tagline') . ': Deep analysis and real-time news on world-changing trends.',
    'archives_empty'    => 'Archives are empty',
    'expect_insights'   => 'Expect new insights very soon.',
    'trending_topics'   => 'Trending Topics',

    // Newsletter
    'newsletter_title'  => config('global.site_name') . ' Weekly',
    'newsletter_desc'   => 'Get the most important tech updates directly to your inbox. No fluff, just value.',
    'email_address'     => 'Email address',
    'subscribe_now'     => 'Subscribe Now',

    // Live Updates / Notifications
    'new_update'    => 'New Update',
    'read_now'      => 'Read Now',
    'dismiss'       => 'Dismiss',

    // Preferences
    'theme'         => 'Theme',
    'language'      => 'Language',
    'preferences'   => 'Preferences',

    // Language switcher
    'switch_lang'   => 'Español',
    'current_lang'  => 'English',

    // SEO defaults
    'site_name'     => config('global.site_name', 'Glodaxia'),
    'meta_desc'     => 'The latest technology, innovation and global trends news, written by experts at ' . config('global.site_name') . '.',

    // E-E-A-T & Transparency
    'read_original_source' => 'Read original source',
    'content_disclaimer'   => 'Disclaimer: The articles on this website may be compiled, translated, or curated with the assistance of artificial intelligence. We strive for maximum accuracy, but recommend verifying details from the original source.',

    // Tag Pages
    'tag_meta_desc' => 'Explore the latest articles, analysis and opinions on #:tag at ' . config('global.site_name') . '.',
    'tag_empty'     => 'No articles found under this tag yet.',
];
