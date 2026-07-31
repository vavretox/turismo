<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductionContentSeeder extends Seeder
{
    /**
     * Importa la instantanea de contenido publico exportada desde desarrollo.
     *
     * Este seeder esta pensado para ejecutarse una sola vez, despues de las
     * migraciones, sobre una base de datos de produccion nueva.
     */
    public function run(): void
    {
        if (DB::table('categorias')->exists()) {
            throw new RuntimeException(
                'ProductionContentSeeder solo puede ejecutarse con las tablas de contenido vacias.'
            );
        }

        $path = database_path('seeders/data/production-content.sql');

        if (! is_file($path) || ! is_readable($path)) {
            throw new RuntimeException("No se puede leer la instantanea de produccion: {$path}");
        }

        $statements = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($statements === false) {
            throw new RuntimeException("No se pudo cargar la instantanea de produccion: {$path}");
        }

        DB::unprepared('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::transaction(function () use ($statements): void {
                foreach ($statements as $statement) {
                    if (! str_starts_with($statement, 'INSERT INTO ')) {
                        throw new RuntimeException('La instantanea contiene una sentencia no permitida.');
                    }

                    DB::unprepared($statement);
                }
            });
        } finally {
            DB::unprepared('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
