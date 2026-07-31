<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('attraction_places', function (Blueprint $table) {
            $table->json('galeria')->nullable()->after('imagen');
            $table->json('service_details')->nullable()->after('room_options');
        });
    }
    public function down(): void {
        Schema::table('attraction_places', fn (Blueprint $table) => $table->dropColumn(['galeria', 'service_details']));
    }
};
