<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('raw_articles', function (Blueprint $table) {
            $table->foreignId('source_id')->nullable()->change();
            $table->string('url')->nullable()->change();
            $table->string('hash')->nullable()->change();
            $table->text('content')->nullable()->change();
            $table->timestamp('published_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_articles', function (Blueprint $table) {
            $table->foreignId('source_id')->nullable(false)->change();
            $table->timestamp('published_at')->nullable(false)->change();
        });
    }
};
