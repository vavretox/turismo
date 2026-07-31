<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->string('fuente_nombre')->nullable()->after('contenido');
            $table->text('fuente_url')->nullable()->after('fuente_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropColumn(['fuente_nombre', 'fuente_url']);
        });
    }
};
