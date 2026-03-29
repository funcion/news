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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_article_id')->nullable()->constrained('raw_articles')->onDelete('set null');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->text('excerpt')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('authors')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('image_url')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->enum('status', ['draft', 'pending_review', 'approved', 'published', 'rejected', 'updated'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->integer('reading_time')->default(0); // minutos
            $table->integer('seo_score')->default(0); // 0-100
            $table->json('ai_metadata')->nullable();
            $table->vector('embedding', 1536)->nullable(); // Para pgvector
            $table->timestamps();
            
            $table->index(['status', 'published_at']);
            $table->index('seo_score');
            $table->index(['category_id', 'published_at']);
            $table->index('views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};