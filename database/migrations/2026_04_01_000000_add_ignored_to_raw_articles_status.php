<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // En PostgreSQL, los enums definidos en la migración original son CHECK constraints.
        // Añadimos 'ignored' a la lista de estados permitidos.
        DB::statement("ALTER TABLE raw_articles DROP CONSTRAINT IF EXISTS raw_articles_status_check");
        DB::statement("ALTER TABLE raw_articles ADD CONSTRAINT raw_articles_status_check CHECK (status IN ('pending', 'processed', 'failed', 'ignored'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE raw_articles DROP CONSTRAINT IF EXISTS raw_articles_status_check");
        DB::statement("ALTER TABLE raw_articles ADD CONSTRAINT raw_articles_status_check CHECK (status IN ('pending', 'processed', 'failed'))");
        
        // Limpiamos los que tengan el estado nuevo antes de revertir
        DB::table('raw_articles')->where('status', 'ignored')->update(['status' => 'failed']);
    }
};
