<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_activities', function (Blueprint $table) {
            $table->json('sectores_interes')->nullable();
            $table->text('horarios')->nullable();
            $table->string('direccion')->nullable();
            $table->string('mapa_url', 1000)->nullable();
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('correo')->nullable();
            $table->string('sitio_web', 1000)->nullable();
            $table->string('facebook', 1000)->nullable();
            $table->string('instagram', 1000)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('weekly_activities', function (Blueprint $table) {
            $table->dropColumn(['sectores_interes', 'horarios', 'direccion', 'mapa_url', 'telefono', 'whatsapp', 'correo', 'sitio_web', 'facebook', 'instagram']);
        });
    }
};
