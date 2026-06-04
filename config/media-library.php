<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Disk Name
    |--------------------------------------------------------------------------
    |
    | The disk where Spatie Media Library stores its files.
    | - 'public' for local storage (dev)
    | - 'r2' for Cloudflare R2 (production)
    |
    | When using R2, set R2_PUBLIC_URL in .env to your CDN domain
    | (e.g., https://media.glodaxia.com) so URLs are served via
    | Cloudflare CDN cache for zero-egress-cost delivery.
    |
    */

    'disk_name' => env('MEDIA_DISK', env('R2_ACCESS_KEY_ID') ? 'r2' : 'public'),

    /*
    |--------------------------------------------------------------------------
    | Max File Size (in bytes)
    |--------------------------------------------------------------------------
    */

    'max_file_size' => 1024 * 1024 * 20, // 20MB

    /*
    |--------------------------------------------------------------------------
    | Media Class
    |--------------------------------------------------------------------------
    */

    'media_model' => Spatie\MediaLibrary\MediaCollections\Models\Media::class,

    /*
    |--------------------------------------------------------------------------
    | Generate conversions on upload (not queued)
    |--------------------------------------------------------------------------
    */

    'queue_connection_name' => env('MEDIA_QUEUE_CONNECTION', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Should the temporary directory be cleaned after queue processing?
    |--------------------------------------------------------------------------
    */

    'temporary_directory_path' => storage_path('app/media-library/temp'),

    /*
    |--------------------------------------------------------------------------
    | When enabled, Media Library will try to optimize the conversion by
    | looking for a source that has already been converted.
    |--------------------------------------------------------------------------
    */

    'optimize_conversions' => true,

    /*
    |--------------------------------------------------------------------------
    | Fallback for when a conversion file is not found.
    |--------------------------------------------------------------------------
    */

    'fallback_mimetypes' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Generator
    |--------------------------------------------------------------------------
    |
    | When using R2 with a custom domain (CDN), Spatie will use the
    | 'url' from the disk config. This means all media URLs will
    | automatically point to your CDN domain.
    |
    */

    'url_generator' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
    |--------------------------------------------------------------------------
    | Prefix for the media URL generator.
    |--------------------------------------------------------------------------
    */

    'prefix_url' => env('R2_PUBLIC_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Media responsive images
    |--------------------------------------------------------------------------
    */

    'responsive_images' => [
        'use_tiny_placeholders' => false,
        'tiny_placeholder_quality' => 30,
    ],



];
