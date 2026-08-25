<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_articles', function (Blueprint $table) {
            if (!Schema::hasColumn('raw_articles', 'ai_model')) {
                $table->string('ai_model')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('raw_articles', function (Blueprint $table) {
            if (Schema::hasColumn('raw_articles', 'ai_model')) {
                $table->dropColumn('ai_model');
            }
        });
    }
};
