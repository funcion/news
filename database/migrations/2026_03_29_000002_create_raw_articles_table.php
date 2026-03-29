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
        Schema::create('raw_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('url');
            $table->text('content');
            $table->text('summary')->nullable();
            $table->string('author')->nullable();
            $table->timestamp('published_at');
            $table->json('categories')->nullable();
            $table->string('image_url')->nullable();
            $table->string('language')->default('es');
            $table->string('hash')->unique(); // Para detección de duplicados
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index('hash');
            $table->index(['source_id', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_articles');
    }
};