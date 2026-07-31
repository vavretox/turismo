<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('provider_offerings', function (Blueprint $table) {
            $table->json('destination_ids')->nullable()->after('incluye');
        });
    }

    public function down(): void
    {
        Schema::table('provider_offerings', function (Blueprint $table) {
            $table->dropColumn('destination_ids');
        });
    }
};
