<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE raw_articles DROP CONSTRAINT IF EXISTS raw_articles_status_check");
        DB::statement("ALTER TABLE raw_articles ADD CONSTRAINT raw_articles_status_check CHECK (status IN ('pending', 'processing', 'processed', 'failed', 'ignored'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE raw_articles DROP CONSTRAINT IF EXISTS raw_articles_status_check");
        DB::statement("ALTER TABLE raw_articles ADD CONSTRAINT raw_articles_status_check CHECK (status IN ('pending', 'processed', 'failed', 'ignored'))");
    }
};