<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_indexable')->default(true)->after('status');
            $table->boolean('is_followable')->default(true)->after('is_indexable');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_indexable')->default(true)->after('is_active');
            $table->boolean('is_followable')->default(true)->after('is_indexable');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->boolean('is_indexable')->default(true)->after('is_featured');
            $table->boolean('is_followable')->default(true)->after('is_indexable');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['is_indexable', 'is_followable']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['is_indexable', 'is_followable']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['is_indexable', 'is_followable']);
        });
    }
};