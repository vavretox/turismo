<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_identities', function (Blueprint $table) {
            $table->string('fuente_texto')->default('Inter');
            $table->string('fuente_titulos')->default('Montserrat');
            $table->unsignedTinyInteger('tamano_texto')->default(16);
            $table->unsignedSmallInteger('peso_texto')->default(400);
            $table->unsignedSmallInteger('peso_titulos')->default(800);
            $table->unsignedSmallInteger('peso_botones')->default(700);
            $table->decimal('espaciado_titulos', 3, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('site_identities', function (Blueprint $table) {
            $table->dropColumn(['fuente_texto', 'fuente_titulos', 'tamano_texto', 'peso_texto', 'peso_titulos', 'peso_botones', 'espaciado_titulos']);
        });
    }
};
