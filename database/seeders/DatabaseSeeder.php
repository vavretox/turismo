<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Destino;
use App\Models\Evento;
use App\Models\Noticia;
use App\Models\PortalImage;
use App\Models\ProvinciaTuristica;
use App\Models\SiteIdentity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if ($adminEmail || $adminPassword) {
            if (! $adminEmail || ! $adminPassword || mb_strlen($adminPassword) < 16) {
                throw new \RuntimeException('ADMIN_EMAIL y ADMIN_PASSWORD (mínimo 16 caracteres) son obligatorios para crear el administrador.');
            }

            User::updateOrCreate(
                ['email' => $adminEmail],
                ['name' => 'Administrador', 'password' => Hash::make($adminPassword), 'is_admin' => true, 'role' => 'admin'],
            );
        }

        SiteIdentity::firstOrCreate(
            ['clave' => 'main'],
            [
                'nombre' => 'Secretaria Departamental de Turismo - GADT',
                'descripcion' => 'Identidad institucional del portal turistico.',
            ],
        );

        $naturaleza = Categoria::firstOrCreate(['nombre' => 'Naturaleza'], ['descripcion' => 'Paisajes, parques y aventura.']);
        $cultura = Categoria::firstOrCreate(['nombre' => 'Cultura'], ['descripcion' => 'Historia, patrimonio y tradiciones.']);
        $gastronomia = Categoria::firstOrCreate(['nombre' => 'Gastronomia'], ['descripcion' => 'Sabores locales, vinos y experiencias culinarias.']);

        foreach ([
            ['nombre' => 'Portada Canaima', 'clave' => 'home_hero_1', 'imagen' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1920&q=85', 'orden' => 1],
            ['nombre' => 'Portada playa', 'clave' => 'home_hero_2', 'imagen' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=85', 'orden' => 2],
            ['nombre' => 'Portada montanas', 'clave' => 'home_hero_3', 'imagen' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1920&q=85', 'orden' => 3],
        ] as $portalImage) {
            PortalImage::firstOrCreate(['clave' => $portalImage['clave']], $portalImage);
        }

        Destino::query()
            ->whereIn('nombre', ['Canaima', 'Los Roques', 'Merida'])
            ->update(['destacado' => false, 'activo' => false]);

        $destinos = [
            [
                'categoria_id' => $gastronomia->id,
                'nombre' => 'Ruta del Vino',
                'resumen' => 'Vinedos, bodegas, singani, gastronomia y paisajes del Valle de la Concepcion.',
                'descripcion' => 'La Ruta del Vino en Uriondo reune bodegas artesanales e industriales, degustaciones, paisajes de valle y experiencias gastronomicas ligadas a la identidad chapaca.',
                'ubicacion' => 'Uriondo',
                'imagen' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=1200&q=80',
                'destacado' => true,
                'orden' => 1,
            ],
            [
                'categoria_id' => $cultura->id,
                'nombre' => 'Casa Vieja',
                'resumen' => 'Historia, arquitectura tradicional y cultura viva de San Lorenzo.',
                'descripcion' => 'Casa Vieja es una referencia patrimonial para conocer la vida tradicional tarijena, sus patios, arquitectura y memoria cultural.',
                'ubicacion' => 'San Lorenzo',
                'imagen' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1200&q=80',
                'destacado' => true,
                'orden' => 2,
            ],
            [
                'categoria_id' => $naturaleza->id,
                'nombre' => 'Reserva Biologica Cordillera de Sama',
                'resumen' => 'Lagunas altoandinas, miradores, rutas naturales y paisajes de altura.',
                'descripcion' => 'La Reserva Biologica Cordillera de Sama protege ecosistemas altoandinos, lagunas, biodiversidad y paisajes ideales para turismo de naturaleza.',
                'ubicacion' => 'Yunchara y Padcaya',
                'imagen' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
                'destacado' => true,
                'orden' => 3,
            ],
        ];

        $destinoNames = array_column($destinos, 'nombre');

        Destino::query()
            ->whereNotIn('nombre', $destinoNames)
            ->update(['destacado' => false]);

        foreach ($destinos as $data) {
            Destino::updateOrCreate(['nombre' => $data['nombre']], $data);
        }

        $provincias = [
            [
                'nombre' => 'Cercado',
                'slug' => 'cercado',
                'subtitulo' => 'Capital cultural de Tarija: San Roque, centro historico, miradores, mercados y vida chapaca.',
                'resumen' => 'Cercado concentra la ciudad de Tarija y varios de sus principales atractivos urbanos, culturales y gastronomicos.',
                'descripcion' => "La provincia Cercado es el punto de entrada natural para descubrir Tarija. Su capital combina plazas, mercados, patrimonio arquitectonico, miradores y una vida urbana tranquila que conserva identidad chapaca.\n\nUno de sus momentos mas representativos es la Fiesta Grande de San Roque, celebrada desde agosto, con los chunchos promesantes recorriendo la ciudad. Esta expresion fue inscrita por la UNESCO como Patrimonio Cultural Inmaterial de la Humanidad en 2021.\n\nEl recorrido turistico puede incluir la Casa Dorada, la Catedral Metropolitana de San Bernardo, el Mercado Central, el Parque Bolivar, la Iglesia de San Roque, el Museo Paleontologico y paseos urbanos como miradores y plazas.",
                'imagen' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1600&q=85',
                'municipios' => "Tarija",
                'atractivos' => "Casa Dorada y Casa de la Cultura\nCatedral Metropolitana de San Bernardo\nIglesia de San Roque\nMercado Central de Tarija\nParque Bolivar\nMuseo Nacional Paleontologico y Arqueologico\nMirador de los Suenos y miradores urbanos",
                'fiestas' => "Fiesta Grande de San Roque y chunchos promesantes\nEntrada de Comadres\nCarnaval Chapaco\nAbril en Tarija\nGastronomia tradicional en mercados y barrios historicos",
                'recomendaciones' => "Visitar el centro historico a pie por la manana\nReservar tiempo para el Mercado Central y la gastronomia local\nConsultar fechas de San Roque entre agosto y septiembre\nCombinar Cercado con rutas cercanas hacia San Lorenzo o Uriondo",
                'orden' => 1,
                'activo' => true,
            ],
            [
                'nombre' => 'Jose Maria Aviles',
                'slug' => 'jose-maria-aviles',
                'subtitulo' => 'Valles, vino, altura y la Ruta del Vino en Uriondo.',
                'resumen' => 'Provincia clave para experiencias vitivinicolas y paisajes altoandinos.',
                'descripcion' => 'Jose Maria Aviles reune el valle de Uriondo, bodegas, vinedos, singani y paisajes de altura hacia Yunchara. Es una provincia ideal para turismo gastronomico, fotografico y de naturaleza.',
                'imagen' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=1600&q=85',
                'municipios' => "Uriondo\nYunchara",
                'atractivos' => "Ruta del Vino\nValle de la Concepcion\nBodegas y vinedos\nPaisajes altoandinos\nLagunas y rutas naturales",
                'fiestas' => "Vendimia chapaca\nExperiencias de degustacion\nFerias productivas locales",
                'recomendaciones' => "Coordinar visitas a bodegas con anticipacion\nLlevar abrigo para zonas altas\nCombinar gastronomia local con recorridos fotograficos",
                'orden' => 2,
                'activo' => true,
            ],
            [
                'nombre' => 'Eustaquio Mendez',
                'slug' => 'eustaquio-mendez',
                'subtitulo' => 'Historia, pueblos tradicionales y cultura chapaca en San Lorenzo y El Puente.',
                'resumen' => 'Provincia de memoria historica, arquitectura tradicional y comunidades rurales.',
                'descripcion' => 'Eustaquio Mendez ofrece una entrada a la Tarija tradicional: San Lorenzo, Casa Vieja, caminos rurales, cultura chapaca y paisajes del norte departamental.',
                'imagen' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1600&q=85',
                'municipios' => "San Lorenzo\nEl Puente",
                'atractivos' => "Casa Vieja\nCentro historico de San Lorenzo\nComunidades rurales\nRutas de altura\nGastronomia tradicional",
                'fiestas' => "Ferias locales\nTradiciones chapacas\nEncuentros culturales comunitarios",
                'recomendaciones' => "Visitar San Lorenzo con tiempo para caminar\nProbar gastronomia local\nConsultar rutas rurales disponibles segun temporada",
                'orden' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'Aniceto Arce',
                'slug' => 'aniceto-arce',
                'subtitulo' => 'Naturaleza, frontera sur, Chaguaya, Padcaya y Bermejo.',
                'resumen' => 'Provincia de rutas naturales, patrimonio religioso y paisajes del sur tarijeno.',
                'descripcion' => 'Aniceto Arce conecta Padcaya, Bermejo, rutas naturales, frontera sur y experiencias vinculadas a la fe, la naturaleza y el clima calido.',
                'imagen' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1600&q=85',
                'municipios' => "Padcaya\nBermejo",
                'atractivos' => "Reserva Biologica Cordillera de Sama\nSantuario de Chaguaya\nRio Bermejo\nPaisajes rurales\nRutas naturales",
                'fiestas' => "Peregrinacion a Chaguaya\nFerias productivas\nExperiencias de frontera",
                'recomendaciones' => "Revisar clima y estado de caminos\nLlevar agua en rutas naturales\nPlanificar la visita a Chaguaya en temporada",
                'orden' => 4,
                'activo' => true,
            ],
            [
                'nombre' => "Burdet O'Connor",
                'slug' => 'burdet-oconnor',
                'subtitulo' => 'Rios, serranias, cultura guarani y naturaleza en Entre Rios.',
                'resumen' => 'Provincia de naturaleza, agua, serranias y cultura viva.',
                'descripcion' => "Burdet O'Connor tiene en Entre Rios un punto de partida para rutas de naturaleza, cultura guarani, bosques, rios y paisajes serranos.",
                'imagen' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1600&q=85',
                'municipios' => "Entre Rios",
                'atractivos' => "Rios y serranias\nCultura guarani\nBosques y naturaleza\nRutas comunitarias",
                'fiestas' => "Encuentros culturales\nFerias locales\nExperiencias comunitarias",
                'recomendaciones' => "Consultar guias locales\nViajar con tiempo para rutas naturales\nRespetar normas comunitarias",
                'orden' => 5,
                'activo' => true,
            ],
            [
                'nombre' => 'Gran Chaco',
                'slug' => 'gran-chaco',
                'subtitulo' => 'Yacuiba, Carapari y Villa Montes: identidad chaquena, Pilcomayo y frontera.',
                'resumen' => 'La region chaquena de Tarija combina cultura, historia, pesca, rios y gastronomia.',
                'descripcion' => 'Gran Chaco abre una experiencia distinta dentro de Tarija: clima calido, historia del Chaco, rio Pilcomayo, gastronomia chaquena, frontera, comercio y naturaleza.',
                'imagen' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1600&q=85',
                'municipios' => "Yacuiba\nCarapari\nVilla Montes",
                'atractivos' => "Rio Pilcomayo\nHistoria del Chaco\nAguas y pozas naturales\nCultura chaquena\nFrontera y comercio",
                'fiestas' => "Ferias chaquenas\nEncuentros gastronomicos\nActividades junto al Pilcomayo",
                'recomendaciones' => "Prever temperaturas altas\nProbar gastronomia chaquena\nConsultar temporadas de pesca y rio",
                'orden' => 6,
                'activo' => true,
            ],
        ];

        $provinciasPorNombre = collect($provincias)->keyBy('nombre');
        $municipios = collect([
            ['Tarija', 'Cercado'], ['Uriondo', 'Jose Maria Aviles'], ['Yunchara', 'Jose Maria Aviles'],
            ['San Lorenzo', 'Eustaquio Mendez'], ['El Puente', 'Eustaquio Mendez'],
            ['Padcaya', 'Aniceto Arce'], ['Bermejo', 'Aniceto Arce'], ['Entre Rios', "Burdet O'Connor"],
            ['Yacuiba', 'Gran Chaco'], ['Carapari', 'Gran Chaco'], ['Villa Montes', 'Gran Chaco'],
        ])->map(function (array $item, int $index) use ($provinciasPorNombre): array {
            [$nombre, $provincia] = $item;
            $source = $provinciasPorNombre->get($provincia, []);

            return array_merge($source, [
                'nombre' => $nombre,
                'slug' => \Illuminate\Support\Str::slug($nombre),
                'provincia' => $provincia,
                'municipios' => null,
                'orden' => $index + 1,
            ]);
        });

        ProvinciaTuristica::query()->whereNotIn('slug', $municipios->pluck('slug'))->delete();
        foreach ($municipios as $municipio) {
            ProvinciaTuristica::updateOrCreate(['slug' => $municipio['slug']], $municipio);
        }

        $destino = Destino::first();

        Evento::firstOrCreate(['titulo' => 'Festival de Experiencias Locales'], [
            'destino_id' => $destino?->id,
            'descripcion' => 'Agenda cultural y gastronomica para visitantes.',
            'lugar' => 'Centro turistico',
            'fecha_inicio' => Carbon::now()->addWeeks(2),
            'activo' => true,
        ]);

        Noticia::firstOrCreate(['titulo' => 'Nuevas rutas para viajeros'], [
            'destino_id' => $destino?->id,
            'resumen' => 'Se incorporan recorridos guiados y puntos de informacion.',
            'contenido' => 'Contenido inicial de noticias turisticas.',
            'imagen' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80',
            'publicado_en' => Carbon::now(),
            'activo' => true,
        ]);
    }
}
