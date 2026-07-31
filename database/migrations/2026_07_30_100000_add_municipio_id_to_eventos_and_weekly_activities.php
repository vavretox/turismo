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
        Schema::table('eventos', function (Blueprint $table) {
            $table->foreignId('municipio_id')
                ->nullable()
                ->after('destino_id')
                ->constrained('provincias_turisticas')
                ->nullOnDelete();
        });

        Schema::table('weekly_activities', function (Blueprint $table) {
            $table->foreignId('municipio_id')
                ->nullable()
                ->after('id')
                ->constrained('provincias_turisticas')
                ->nullOnDelete();
        });

        $municipios = DB::table('provincias_turisticas')->get(['id', 'nombre', 'provincia']);
        $normalizar = fn (?string $texto): string => Str::ascii(mb_strtolower((string) $texto));
        $encontrarMunicipio = function (string $texto) use ($municipios, $normalizar) {
            return $municipios->first(
                fn ($municipio): bool => str_contains($texto, $normalizar($municipio->nombre))
            );
        };

        DB::table('eventos')
            ->leftJoin('destinos', 'destinos.id', '=', 'eventos.destino_id')
            ->select('eventos.id', 'eventos.lugar', 'eventos.descripcion', 'destinos.municipio_id as destino_municipio_id')
            ->orderBy('eventos.id')
            ->each(function ($evento) use ($encontrarMunicipio, $normalizar): void {
                $municipioId = $evento->destino_municipio_id;
                if (! $municipioId) {
                    $texto = $normalizar($evento->lugar.' '.$evento->descripcion);
                    $municipioId = $encontrarMunicipio($texto)?->id;
                }
                if ($municipioId) {
                    DB::table('eventos')->where('id', $evento->id)->update(['municipio_id' => $municipioId]);
                }
            });

        DB::table('weekly_activities')
            ->select('id', 'lugar', 'direccion', 'titulo', 'descripcion')
            ->orderBy('id')
            ->each(function ($actividad) use ($encontrarMunicipio, $normalizar): void {
                $texto = $normalizar(implode(' ', [
                    $actividad->lugar,
                    $actividad->direccion,
                    $actividad->titulo,
                    $actividad->descripcion,
                ]));
                $municipioId = $encontrarMunicipio($texto)?->id;
                if ($municipioId) {
                    DB::table('weekly_activities')->where('id', $actividad->id)->update(['municipio_id' => $municipioId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('weekly_activities', fn (Blueprint $table) => $table->dropConstrainedForeignId('municipio_id'));
        Schema::table('eventos', fn (Blueprint $table) => $table->dropConstrainedForeignId('municipio_id'));
    }
};
