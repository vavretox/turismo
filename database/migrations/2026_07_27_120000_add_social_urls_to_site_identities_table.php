<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_identities', function (Blueprint $table) {
            $table->string('facebook_url', 1000)->nullable();
            $table->string('instagram_url', 1000)->nullable();
            $table->string('x_url', 1000)->nullable();
            $table->string('youtube_url', 1000)->nullable();
            $table->string('tiktok_url', 1000)->nullable();
            $table->string('whatsapp_url', 1000)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_identities', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_url',
                'instagram_url',
                'x_url',
                'youtube_url',
                'tiktok_url',
                'whatsapp_url',
            ]);
        });
    }
};
