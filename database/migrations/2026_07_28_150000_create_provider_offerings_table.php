<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('provider_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tourism_service_provider_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->text('resumen');
            $table->longText('descripcion')->nullable();
            $table->string('imagen');
            $table->json('galeria')->nullable();
            $table->string('duracion')->nullable();
            $table->string('precio')->nullable();
            $table->text('incluye')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
            $table->index(['tourism_service_provider_id', 'activo', 'orden'], 'provider_offerings_public_index');
        });
    }
    public function down(): void { Schema::dropIfExists('provider_offerings'); }
};
