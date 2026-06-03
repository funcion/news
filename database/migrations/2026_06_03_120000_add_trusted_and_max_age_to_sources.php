<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->boolean('trusted')->default(false)->after('is_active');
            $table->integer('max_age_days')->default(7)->after('trusted')->comment('Reject articles older than this many days');
        });
    }

    public function down(): void
    {
        Schema::table('sources', function (Blueprint $table) {
            $table->dropColumn(['trusted', 'max_age_days']);
        });
    }
};
