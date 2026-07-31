<?php

namespace Database\Seeders;

use App\Models\WeeklyActivity;
use Illuminate\Database\Seeder;

class WeeklyActivitySeeder extends Seeder
{
    public function run(): void
    {
        WeeklyActivity::firstOrCreate(
            ['titulo' => 'Descubre las actividades de esta semana'],
            [
                'subtitulo' => 'Agenda turística de Tarija',
                'descripcion' => 'Consulta las experiencias, eventos y recorridos preparados para disfrutar Tarija esta semana. Este anuncio puede editarse o reemplazarse desde el panel administrativo.',
                'imagen' => '/images/referencia/tarija.jpg',
                'texto_boton' => 'Ver agenda turística',
                'enlace' => '/eventos',
                'orden' => 1,
                'activo' => true,
            ],
        );
    }
}
