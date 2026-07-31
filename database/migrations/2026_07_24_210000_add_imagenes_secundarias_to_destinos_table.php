<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->json('imagenes_secundarias')->nullable()->after('imagen');
        });
    }

    public function down(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->dropColumn('imagenes_secundarias');
        });
    }
};
