<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_activities', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('subtitulo')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->string('lugar')->nullable();
            $table->dateTime('fecha_actividad')->nullable();
            $table->dateTime('visible_desde')->nullable();
            $table->dateTime('visible_hasta')->nullable();
            $table->string('texto_boton')->nullable();
            $table->string('enlace')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_activities');
    }
};
