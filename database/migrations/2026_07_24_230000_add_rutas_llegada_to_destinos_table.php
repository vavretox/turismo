<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->json('rutas_llegada')->nullable()->after('como_llegar');
        });
    }

    public function down(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->dropColumn('rutas_llegada');
        });
    }
};
