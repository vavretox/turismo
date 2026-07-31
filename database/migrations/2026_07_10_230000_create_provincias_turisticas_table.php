<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provincias_turisticas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('subtitulo')->nullable();
            $table->text('resumen')->nullable();
            $table->longText('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->text('municipios')->nullable();
            $table->longText('atractivos')->nullable();
            $table->longText('fiestas')->nullable();
            $table->longText('recomendaciones')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provincias_turisticas');
    }
};
