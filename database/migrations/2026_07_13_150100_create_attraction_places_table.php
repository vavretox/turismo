<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('attraction_places', function (Blueprint $table) { $table->id(); $table->foreignId('attraction_type_id')->constrained()->cascadeOnDelete(); $table->string('titulo'); $table->string('slug')->unique(); $table->text('resumen')->nullable(); $table->longText('descripcion')->nullable(); $table->string('imagen')->nullable(); $table->decimal('latitud', 10, 7); $table->decimal('longitud', 10, 7); $table->string('direccion')->nullable(); $table->string('telefono')->nullable(); $table->string('sitio_web')->nullable(); $table->string('horario')->nullable(); $table->string('precio')->nullable(); $table->boolean('destacado')->default(false); $table->boolean('activo')->default(true); $table->unsignedInteger('orden')->default(0); $table->timestamps(); }); }
    public function down(): void { Schema::dropIfExists('attraction_places'); }
};
