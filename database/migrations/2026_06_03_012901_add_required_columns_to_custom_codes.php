<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_codes', function (Blueprint $table) {
            $table->string('location');
            $table->text('content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('custom_codes', function (Blueprint $table) {
            $table->dropColumn(['location', 'content', 'is_active', 'description']);
        });
    }
};