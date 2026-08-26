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
        Schema::table('users', function (Blueprint $table) {
            // Identidad y Empresa
            $table->string('identification_number')->nullable()->after('surname');
            $table->string('company')->nullable()->after('identification_number');

            // Teléfonos (Obligatorios según requerimiento)
            $table->string('phone')->nullable()->after('company');
            $table->string('office_phone')->nullable()->after('phone');
            $table->string('home_phone')->nullable()->after('office_phone');
            $table->string('whatsapp')->nullable()->after('home_phone');

            // Redes Sociales (Opcionales)
            $table->string('facebook_url')->nullable()->after('whatsapp');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('pinterest_url')->nullable()->after('instagram_url');
            $table->string('linkedin_url')->nullable()->after('pinterest_url');
            $table->string('tiktok_url')->nullable()->after('linkedin_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'identification_number',
                'company',
                'phone',
                'office_phone',
                'home_phone',
                'whatsapp',
                'facebook_url',
                'instagram_url',
                'pinterest_url',
                'linkedin_url',
                'tiktok_url'
            ]);
        });
    }
};
