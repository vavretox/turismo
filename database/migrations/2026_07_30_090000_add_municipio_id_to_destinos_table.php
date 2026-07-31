<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->foreignId('municipio_id')
                ->nullable()
                ->after('categoria_id')
                ->constrained('provincias_turisticas')
                ->nullOnDelete();
        });

        $municipios = DB::table('provincias_turisticas')->get(['id', 'nombre', 'provincia']);

        DB::table('destinos')
            ->whereNull('municipio_id')
            ->orderBy('id')
            ->each(function ($destino) use ($municipios): void {
                $ubicacion = Str::ascii(mb_strtolower((string) $destino->ubicacion));
                $municipio = $municipios->first(function ($item) use ($ubicacion): bool {
                    $nombre = Str::ascii(mb_strtolower($item->nombre));

                    return $ubicacion !== '' && str_contains($ubicacion, $nombre);
                });

                if ($municipio) {
                    DB::table('destinos')->where('id', $destino->id)->update([
                        'municipio_id' => $municipio->id,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('destinos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('municipio_id');
        });
    }
};
