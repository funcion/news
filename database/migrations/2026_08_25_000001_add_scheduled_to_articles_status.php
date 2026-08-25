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
        DB::statement("ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_status_check");
        DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_status_check CHECK (status IN ('draft', 'scheduled', 'pending_review', 'approved', 'published', 'rejected', 'updated'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_status_check");
        DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_status_check CHECK (status IN ('draft', 'pending_review', 'approved', 'published', 'rejected', 'updated'))");
    }
};