<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destino_id')->nullable()->constrained('destinos')->nullOnDelete();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->string('resumen')->nullable();
            $table->longText('contenido')->nullable();
            $table->string('imagen')->nullable();
            $table->dateTime('publicado_en')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};
