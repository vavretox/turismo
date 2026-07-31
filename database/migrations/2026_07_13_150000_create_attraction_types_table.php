<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('attraction_types', function (Blueprint $table) { $table->id(); $table->foreignId('parent_id')->nullable()->constrained('attraction_types')->nullOnDelete(); $table->string('nombre'); $table->string('slug')->unique(); $table->string('icono')->default('fa-location-dot'); $table->string('color', 20)->default('#991b1b'); $table->text('descripcion')->nullable(); $table->text('que_hacer')->nullable(); $table->unsignedInteger('orden')->default(0); $table->boolean('activo')->default(true); $table->timestamps(); }); }
    public function down(): void { Schema::dropIfExists('attraction_types'); }
};
