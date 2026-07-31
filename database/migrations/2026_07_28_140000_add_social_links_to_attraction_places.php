<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('attraction_places', function (Blueprint $table) {
            $table->string('whatsapp')->nullable()->after('telefono');
            $table->string('facebook')->nullable()->after('sitio_web');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('tiktok')->nullable()->after('instagram');
            $table->string('x_url')->nullable()->after('tiktok');
        });
    }
    public function down(): void {
        Schema::table('attraction_places', fn (Blueprint $table) => $table->dropColumn(['whatsapp', 'facebook', 'instagram', 'tiktok', 'x_url']));
    }
};
