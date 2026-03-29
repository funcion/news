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
        Schema::create('article_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->text('summary')->nullable();
            $table->string('source_url')->nullable();
            $table->timestamp('published_at');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['article_id', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_updates');
    }
};