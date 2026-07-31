<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->string('subtitulo')->nullable()->after('nombre');
            $table->text('introduccion')->nullable()->after('resumen');
            $table->text('como_llegar')->nullable()->after('descripcion');
            $table->string('mejor_epoca')->nullable()->after('como_llegar');
            $table->string('duracion_recomendada')->nullable()->after('mejor_epoca');
            $table->text('recomendaciones')->nullable()->after('duracion_recomendada');
        });
    }

    public function down(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->dropColumn([
                'subtitulo',
                'introduccion',
                'como_llegar',
                'mejor_epoca',
                'duracion_recomendada',
                'recomendaciones',
            ]);
        });
    }
};
