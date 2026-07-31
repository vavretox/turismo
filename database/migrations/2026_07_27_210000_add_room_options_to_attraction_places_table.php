<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attraction_places', function (Blueprint $table) {
            $table->json('room_options')->nullable()->after('precio');
        });
    }

    public function down(): void
    {
        Schema::table('attraction_places', function (Blueprint $table) {
            $table->dropColumn('room_options');
        });
    }
};
