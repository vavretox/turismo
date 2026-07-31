<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tourism_service_providers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('attraction_place_id')->nullable()->unique()->after('user_id')->constrained()->nullOnDelete();
        });
        Schema::table('attraction_places', function (Blueprint $table) {
            $table->foreignId('tourism_service_provider_id')->nullable()->unique()->after('id')->constrained()->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('attraction_places', fn (Blueprint $table) => $table->dropConstrainedForeignId('tourism_service_provider_id'));
        Schema::table('tourism_service_providers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attraction_place_id');
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
