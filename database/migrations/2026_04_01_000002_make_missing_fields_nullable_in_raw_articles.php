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
            $table->string('url')->nullable()->change();
            $table->string('hash')->nullable()->change();
            $table->text('content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_articles', function (Blueprint $table) {
            $table->string('url')->nullable(false)->change();
            $table->string('hash')->nullable(false)->change();
            $table->text('content')->nullable(false)->change();
        });
    }
};
