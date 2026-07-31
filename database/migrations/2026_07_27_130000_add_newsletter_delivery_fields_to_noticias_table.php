<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->timestamp('newsletter_enviado_en')->nullable()->after('activo');
            $table->unsignedInteger('newsletter_destinatarios')->default(0)->after('newsletter_enviado_en');
            $table->unsignedInteger('newsletter_fallidos')->default(0)->after('newsletter_destinatarios');
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropColumn([
                'newsletter_enviado_en',
                'newsletter_destinatarios',
                'newsletter_fallidos',
            ]);
        });
    }
};
