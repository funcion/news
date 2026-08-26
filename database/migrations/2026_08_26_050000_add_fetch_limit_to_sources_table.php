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
        if (Schema::hasTable('sources') && !Schema::hasColumn('sources', 'fetch_limit')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->unsignedInteger('fetch_limit')->default(3)->after('frequency');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sources') && Schema::hasColumn('sources', 'fetch_limit')) {
            Schema::table('sources', function (Blueprint $table) {
                $table->dropColumn('fetch_limit');
            });
        }
    }
};