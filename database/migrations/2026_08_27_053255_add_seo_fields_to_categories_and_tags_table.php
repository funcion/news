<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->jsonb('meta_title')->nullable()->after('description');
            $table->jsonb('meta_description')->nullable()->after('meta_title');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->jsonb('meta_title')->nullable()->after('description');
            $table->jsonb('meta_description')->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });
    }
};