<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert article text columns to JSONB for spatie/laravel-translatable.
     * Primary language: English (en). Secondary: Spanish (es).
     */
    public function up(): void
    {
        // Step 1: Add new JSONB columns alongside existing ones
        Schema::table('articles', function (Blueprint $table) {
            $table->jsonb('title_i18n')->nullable()->after('title');
            $table->jsonb('content_i18n')->nullable()->after('content');
            $table->jsonb('excerpt_i18n')->nullable()->after('excerpt');
            $table->jsonb('meta_title_i18n')->nullable()->after('meta_title');
            $table->jsonb('meta_description_i18n')->nullable()->after('meta_description');
            $table->jsonb('image_alt_i18n')->nullable()->after('image_alt');
            $table->string('slug_en')->nullable()->after('slug');
            $table->string('slug_es')->nullable()->after('slug_en');
        });

        // Step 2: Migrate existing Spanish data into the JSON columns
        DB::statement("UPDATE articles SET
            title_i18n = jsonb_build_object('es', title),
            content_i18n = jsonb_build_object('es', content),
            excerpt_i18n = CASE WHEN excerpt IS NOT NULL THEN jsonb_build_object('es', excerpt) ELSE NULL END,
            meta_title_i18n = CASE WHEN meta_title IS NOT NULL THEN jsonb_build_object('es', meta_title) ELSE NULL END,
            meta_description_i18n = CASE WHEN meta_description IS NOT NULL THEN jsonb_build_object('es', meta_description) ELSE NULL END,
            image_alt_i18n = CASE WHEN image_alt IS NOT NULL THEN jsonb_build_object('es', image_alt) ELSE NULL END,
            slug_es = slug,
            slug_en = slug
        ");

        // Step 3: Drop old columns and rename new ones
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'excerpt', 'meta_title', 'meta_description', 'image_alt', 'slug']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->renameColumn('title_i18n', 'title');
            $table->renameColumn('content_i18n', 'content');
            $table->renameColumn('excerpt_i18n', 'excerpt');
            $table->renameColumn('meta_title_i18n', 'meta_title');
            $table->renameColumn('meta_description_i18n', 'meta_description');
            $table->renameColumn('image_alt_i18n', 'image_alt');
        });

        // Step 4: Add unique indexes for slugs
        Schema::table('articles', function (Blueprint $table) {
            $table->unique('slug_en');
            $table->unique('slug_es');
        });
    }

    public function down(): void
    {
        // Reverse: convert back to simple string columns (loses translations)
        Schema::table('articles', function (Blueprint $table) {
            $table->string('title_tmp')->nullable();
            $table->text('content_tmp')->nullable();
        });

        DB::statement("UPDATE articles SET
            title_tmp = title->>'en',
            content_tmp = content->>'en'
        ");

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title', 'content', 'excerpt', 'meta_title', 'meta_description', 'image_alt', 'slug_en', 'slug_es']);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->renameColumn('title_tmp', 'title');
            $table->renameColumn('content_tmp', 'content');
            $table->string('slug')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('image_alt')->nullable();
        });
    }
};
