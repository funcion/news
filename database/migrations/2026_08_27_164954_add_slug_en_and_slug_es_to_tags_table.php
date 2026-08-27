<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            if (!Schema::hasColumn('tags', 'slug_en')) {
                $table->string('slug_en')->nullable()->index()->after('slug');
            }
            if (!Schema::hasColumn('tags', 'slug_es')) {
                $table->string('slug_es')->nullable()->index()->after('slug_en');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn(['slug_en', 'slug_es']);
        });
    }
};