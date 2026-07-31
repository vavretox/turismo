<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tourism_service_providers', function (Blueprint $table): void {
            $table->string('x_url')->nullable()->after('tiktok');
            $table->string('youtube_url')->nullable()->after('x_url');
        });

        Schema::table('attraction_places', function (Blueprint $table): void {
            $table->string('youtube_url')->nullable()->after('x_url');
        });

        Schema::table('weekly_activities', function (Blueprint $table): void {
            $table->string('x_url', 1000)->nullable()->after('instagram');
            $table->string('youtube_url', 1000)->nullable()->after('x_url');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_activities', fn (Blueprint $table) => $table->dropColumn(['x_url', 'youtube_url']));
        Schema::table('attraction_places', fn (Blueprint $table) => $table->dropColumn('youtube_url'));
        Schema::table('tourism_service_providers', fn (Blueprint $table) => $table->dropColumn(['x_url', 'youtube_url']));
    }
};
