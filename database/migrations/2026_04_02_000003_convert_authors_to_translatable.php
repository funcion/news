<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert author text columns to JSONB for spatie/laravel-translatable.
     */
    public function up(): void
    {
        // Step 1: Add new JSONB columns alongside existing ones
        Schema::table('authors', function (Blueprint $table) {
            $table->jsonb('name_i18n')->nullable()->after('name');
            $table->jsonb('bio_i18n')->nullable()->after('bio');
        });

        // Step 2: Migrate existing data to JSON (assuming current data is in Spanish as seen in the model)
        // Note: The system seems to be transitioning from Spanish as primary to Bilingual.
        // We'll put existing content into both locales if possible, or just the current app locale.
        DB::statement("UPDATE authors SET
            name_i18n = jsonb_build_object('es', name, 'en', name),
            bio_i18n = CASE WHEN bio IS NOT NULL THEN jsonb_build_object('es', bio, 'en', bio) ELSE NULL END
        ");

        // Step 3: Drop old columns
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn(['name', 'bio']);
        });

        // Step 4: Rename new columns to original names
        Schema::table('authors', function (Blueprint $table) {
            $table->renameColumn('name_i18n', 'name');
            $table->renameColumn('bio_i18n', 'bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('name_tmp')->nullable();
            $table->text('bio_tmp')->nullable();
        });

        DB::statement("UPDATE authors SET
            name_tmp = name->>'es',
            bio_tmp = bio->>'es'
        ");

        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn(['name', 'bio']);
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->renameColumn('name_tmp', 'name');
            $table->renameColumn('bio_tmp', 'bio');
        });
    }
};
