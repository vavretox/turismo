<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourism_service_providers', function (Blueprint $table) {
            $table->unique('commercial_name', 'tourism_service_providers_commercial_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tourism_service_providers', function (Blueprint $table) {
            $table->dropUnique('tourism_service_providers_commercial_name_unique');
        });
    }
};
