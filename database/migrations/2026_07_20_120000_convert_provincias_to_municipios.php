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
        Schema::table('provincias_turisticas', function (Blueprint $table) {
            $table->string('provincia')->nullable()->after('slug');
        });

        $municipios = [
            ['Tarija', 'Cercado'],
            ['Uriondo', 'José María Avilés'],
            ['Yunchará', 'José María Avilés'],
            ['San Lorenzo', 'Eustaquio Méndez'],
            ['El Puente', 'Eustaquio Méndez'],
            ['Padcaya', 'Aniceto Arce'],
            ['Bermejo', 'Aniceto Arce'],
            ['Entre Ríos', "Burdet O'Connor"],
            ['Yacuiba', 'Gran Chaco'],
            ['Caraparí', 'Gran Chaco'],
            ['Villa Montes', 'Gran Chaco'],
        ];

        $sources = DB::table('provincias_turisticas')->get()->keyBy(
            fn ($row) => Str::ascii(mb_strtolower($row->nombre)),
        );

        DB::table('provincias_turisticas')->delete();

        foreach ($municipios as $index => [$nombre, $provincia]) {
            $source = $sources->get(Str::ascii(mb_strtolower($provincia)));
            DB::table('provincias_turisticas')->insert([
                'nombre' => $nombre,
                'slug' => Str::slug($nombre),
                'provincia' => $provincia,
                'subtitulo' => $source?->subtitulo ?: "Descubre la identidad, cultura y paisajes de {$nombre}.",
                'resumen' => $source?->resumen ?: "Conoce los principales atractivos y experiencias turísticas de {$nombre}.",
                'descripcion' => $source?->descripcion,
                'imagen' => $source?->imagen,
                'municipios' => null,
                'atractivos' => $source?->atractivos,
                'fiestas' => $source?->fiestas,
                'recomendaciones' => $source?->recomendaciones,
                'orden' => $index + 1,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('provincias_turisticas', function (Blueprint $table) {
            $table->dropColumn('provincia');
        });
    }
};
