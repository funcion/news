<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Convert users.name to jsonb so it can store bilingual translations
        if (Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->jsonb('name_i18n')->nullable();
            });

            // Copy existing name string into translation format
            DB::statement("UPDATE users SET name_i18n = jsonb_build_object('es', name, 'en', name)");

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name');
            });

            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('name_i18n', 'name');
            });
        }

        // 2. Add other author columns to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->jsonb('bio')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url')->nullable();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        // 3. Update articles table: add user_id and migrate existing author_id values
        if (Schema::hasColumn('articles', 'author_id') && !Schema::hasColumn('articles', 'user_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable();
            });

            // Set user_id equal to author_id or fallback to 1
            DB::statement("UPDATE articles SET user_id = author_id");
            DB::statement("UPDATE articles SET user_id = 1 WHERE user_id IS NULL OR NOT EXISTS (SELECT 1 FROM users WHERE users.id = articles.user_id)");

            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('author_id');
            });
        }

        // 4. Drop authors table
        Schema::dropIfExists('authors');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non-reversible
    }
};
