<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- CATEGORIES ---
        Schema::table('categories', function (Blueprint $table) {
            $table->jsonb('name_i18n')->nullable()->after('name');
            $table->jsonb('description_i18n')->nullable()->after('description');
            $table->string('slug_en')->nullable()->after('slug');
            $table->string('slug_es')->nullable()->after('slug_en');
        });

        DB::statement("UPDATE categories SET
            name_i18n = jsonb_build_object('es', name),
            description_i18n = CASE WHEN description IS NOT NULL THEN jsonb_build_object('es', description) ELSE NULL END,
            slug_es = slug,
            slug_en = slug
        ");

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'slug']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name_i18n', 'name');
            $table->renameColumn('description_i18n', 'description');
            $table->unique('slug_en');
            $table->unique('slug_es');
        });

        // --- TAGS ---
        Schema::table('tags', function (Blueprint $table) {
            $table->jsonb('name_i18n')->nullable()->after('name');
            $table->jsonb('description_i18n')->nullable()->after('description');
        });

        DB::statement("UPDATE tags SET
            name_i18n = jsonb_build_object('es', name),
            description_i18n = CASE WHEN description IS NOT NULL THEN jsonb_build_object('es', description) ELSE NULL END
        ");

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->renameColumn('name_i18n', 'name');
            $table->renameColumn('description_i18n', 'description');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'slug_en', 'slug_es']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('slug')->unique();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
            $table->string('name');
            $table->text('description')->nullable();
        });
    }
};
