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
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('type')->default('rss'); // rss, api, scraping
            $table->string('category')->nullable();
            $table->integer('frequency')->default(30); // minutes
            $table->timestamp('last_fetched_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('score')->default(50); // 0-100
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'last_fetched_at']);
            $table->index('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};