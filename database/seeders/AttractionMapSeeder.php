<?php

namespace Database\Seeders;

use App\Models\AttractionPlace;
use App\Models\AttractionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttractionMapSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'alojamiento' => ['Alojamiento', 'fa-bed', '#7c3aed', 1, 'Hoteles, hostales y estancias para planificar la visita.'],
            'gastronomia' => ['Gastronomía', 'fa-utensils', '#96545d', 2, 'Mercados, restaurantes, bodegas y sabores tradicionales.'],
            'cultura' => ['Cultura y patrimonio', 'fa-landmark', '#991b1b', 3, 'Museos, templos, plazas y patrimonio histórico.'],
            'naturaleza' => ['Naturaleza', 'fa-tree', '#15803d', 4, 'Reservas, lagunas, ríos, cascadas y paisajes.'],
        ];

        $types = [];
        foreach ($categories as $slug => [$name, $icon, $color, $order, $description]) {
            $types[$slug] = AttractionType::updateOrCreate(['slug' => $slug], [
                'nombre' => $name,
                'icono' => $icon,
                'color' => $color,
                'descripcion' => $description,
                'que_hacer' => 'Consultar el punto en el mapa, revisar su información y usar la opción Cómo llegar.',
                'orden' => $order,
                'activo' => true,
            ]);
        }

        $subtypes = [
            'hoteles' => ['alojamiento', 'Hoteles', 'fa-hotel', '#8b5cf6'],
            'hospedaje-rural' => ['alojamiento', 'Hospedaje rural', 'fa-bed', '#6d28d9'],
            'comida-tradicional' => ['gastronomia', 'Comida tradicional', 'fa-utensils', '#c2410c'],
            'mercados' => ['gastronomia', 'Mercados', 'fa-cart-shopping', '#ea580c'],
            'bodegas-y-vinos' => ['gastronomia', 'Bodegas y vinos', 'fa-wine-glass', '#9f1239'],
            'museos-y-casas-historicas' => ['cultura', 'Museos y casas históricas', 'fa-landmark', '#b91c1c'],
            'templos' => ['cultura', 'Templos y santuarios', 'fa-church', '#7f1d1d'],
            'plazas-y-pueblos' => ['cultura', 'Plazas y pueblos', 'fa-location-dot', '#be123c'],
            'reservas-y-lagunas' => ['naturaleza', 'Reservas y lagunas', 'fa-tree', '#166534'],
            'rios-y-cascadas' => ['naturaleza', 'Ríos y cascadas', 'fa-tree', '#0369a1'],
            'miradores-y-canones' => ['naturaleza', 'Miradores y cañones', 'fa-mountain-sun', '#0f766e'],
        ];

        foreach ($subtypes as $slug => [$parent, $name, $icon, $color]) {
            $types[$slug] = AttractionType::updateOrCreate(['slug' => $slug], [
                'parent_id' => $types[$parent]->id,
                'nombre' => $name,
                'icono' => $icon,
                'color' => $color,
                'descripcion' => $name . ' del departamento de Tarija.',
                'que_hacer' => 'Seleccionar un lugar para ver detalles, ubicación y alternativas para llegar.',
                'activo' => true,
            ]);
        }

        $places = [
            // Provincia Cercado
            ['Centro histórico de Tarija', 'plazas-y-pueblos', -21.5355, -64.7296, 'Centro, Tarija — provincia Cercado', 'Plazas, arquitectura republicana, cafés y vida cultural en el corazón de la capital.', true],
            ['Casa Dorada', 'museos-y-casas-historicas', -21.5347, -64.7335, 'General Trigo esquina Ingavi, Tarija — Cercado', 'Patrimonio arquitectónico nacional construido a comienzos del siglo XX.', true],
            ['Museo Paleontológico y Arqueológico', 'museos-y-casas-historicas', -21.5329, -64.7300, 'General Trigo 402, Tarija — Cercado', 'Colecciones paleontológicas, arqueológicas y geológicas vinculadas al valle de Tarija.', false],
            ['Iglesia de San Roque', 'templos', -21.5312, -64.7357, 'Barrio San Roque, Tarija — Cercado', 'Templo asociado a la Fiesta Grande de Tarija y a la tradición de los chunchos.', true],
            ['Lago San Jacinto', 'reservas-y-lagunas', -21.6066917, -64.7363250, 'San Jacinto, Tarija — Cercado', 'Embalse del valle central con paisaje, paseos y gastronomía en sus orillas.', true],
            ['Observatorio Astronómico Nacional', 'museos-y-casas-historicas', -21.6007, -64.6233, 'Santa Ana, Tarija — Cercado', 'Centro de observación y divulgación astronómica en la campiña tarijeña.', false],
            ['Mercado Central de Tarija', 'mercados', -21.5344, -64.7308, 'Centro de Tarija — Cercado', 'Espacio para descubrir productos locales, desayunos y platos de la cocina chapaca.', false],
            ['Hotel Los Ceibos', 'hoteles', -21.5419, -64.7288, 'Av. Panamericana 612, Tarija — Cercado', 'Hotel urbano con servicios de hospedaje, restaurante y espacios de bienestar.', false, '+591 75133800', 'https://hotellosceibos.com/'],
            ['Los Parrales Hotel Resort', 'hoteles', -21.5057, -64.7508, 'Urbanización El Carmen de Aranjuez km 3,5 — Cercado', 'Hotel resort en un entorno tranquilo del valle de Tarija.', false, '+591 75111885', 'https://losparraleshotel.com/'],

            // Provincia José María Avilés
            ['Valle de la Concepción', 'plazas-y-pueblos', -21.6964, -64.6567, 'Uriondo — provincia José María Avilés', 'Pueblo y paisaje vitivinícola central de la Ruta del Vino y Singani de Altura.', true],
            ['Casa Vieja', 'bodegas-y-vinos', -21.6978, -64.6585, 'Valle de la Concepción, Uriondo — José María Avilés', 'Bodega tradicional conocida por sus patios, vinos artesanales y memoria del valle.', true],
            ['Bodega y viñedos de Uriondo', 'bodegas-y-vinos', -21.7040, -64.6518, 'Uriondo — José María Avilés', 'Zona de viñedos donde se conoce la producción de vinos y singanis de altura.', false],
            ['Lagunas de Tajzara', 'reservas-y-lagunas', -21.78935, -65.08986, 'Yunchará — provincia José María Avilés', 'Lagunas altoandinas de la Pampa de Tajzara, hábitat de flamencos y fauna de altura.', true],
            ['Dunas de Tajzara', 'miradores-y-canones', -21.8150, -65.0830, 'Pampa de Tajzara, Yunchará — José María Avilés', 'Formaciones arenosas de altura para contemplación del paisaje y fotografía.', false],
            ['Camino preincaico de Calderillas', 'miradores-y-canones', -21.7200, -65.0200, 'Abra de Calderillas — José María Avilés', 'Sendero histórico de montaña que requiere planificación y acompañamiento local.', false],
            ['Posada del Viñatero', 'hospedaje-rural', -21.6995, -64.6575, 'Valle de la Concepción, Uriondo — José María Avilés', 'Hospedaje vinculado a la experiencia vitivinícola del Valle de la Concepción.', false],

            // Provincia Eustaquio Méndez
            ['San Lorenzo', 'plazas-y-pueblos', -21.4169, -64.7503, 'San Lorenzo — provincia Eustaquio Méndez', 'Pueblo colonial de calles tradicionales, pan casero, rosquetes y cultura chapaca.', true],
            ['Casa del Moto Méndez', 'museos-y-casas-historicas', -21.4174, -64.7494, 'San Lorenzo — provincia Eustaquio Méndez', 'Casa museo dedicada al héroe de la independencia Eustaquio Méndez.', true],
            ['Coimata', 'rios-y-cascadas', -21.4890, -64.7900, 'Coimata, San Lorenzo — Eustaquio Méndez', 'Balneario natural de aguas claras y entorno arbolado cerca de Tomatitas.', true],
            ['Chorros de Jurina', 'rios-y-cascadas', -21.3690, -64.8210, 'Jurina, San Lorenzo — Eustaquio Méndez', 'Cascadas y pozas naturales visitadas mediante recorridos de naturaleza.', false],
            ['Marquiri', 'rios-y-cascadas', -21.3310, -64.7820, 'Calama, San Lorenzo — Eustaquio Méndez', 'Sendero entre serranías que conduce a una caída de agua y pozas naturales.', false],
            ['Cañón del Pilaya', 'miradores-y-canones', -21.2420, -65.1260, 'Límite occidental de Tarija — Eustaquio Méndez', 'Gran formación natural para observación panorámica y turismo de aventura organizado.', true],
            ['Tomatitas gastronómica', 'comida-tradicional', -21.4678, -64.7505, 'Tomatitas, San Lorenzo — Eustaquio Méndez', 'Zona tradicional para degustar cangrejos, misquinchos y platos del valle.', false],

            // Provincia Aniceto Arce
            ['Padcaya', 'plazas-y-pueblos', -21.8833, -64.7147, 'Padcaya — provincia Aniceto Arce', 'Pueblo de arquitectura tradicional y punto de conexión hacia Chaguaya y Bermejo.', false],
            ['Parroquia Purísima Concepción de Padcaya', 'templos', -21.8831, -64.7145, 'Plaza principal de Padcaya — Aniceto Arce', 'Templo histórico construido en el siglo XVIII con patrimonio religioso regional.', true],
            ['Santuario de Chaguaya', 'templos', -22.0087, -64.7447, 'Chaguaya, Padcaya — Aniceto Arce', 'Santuario de peregrinación mariana y una de las expresiones religiosas de Tarija.', true],
            ['Valle de los Cóndores', 'miradores-y-canones', -22.0550, -64.8200, 'Abra de San Miguel de Chaguaya — Aniceto Arce', 'Paisaje montañoso para observación responsable del cóndor andino con guías locales.', true],
            ['Bermejo', 'plazas-y-pueblos', -22.7322, -64.3426, 'Bermejo — provincia Aniceto Arce', 'Ciudad fronteriza rodeada de ríos, cultivos subtropicales y tradición azucarera.', false],
            ['Balneario El Chorro de Bermejo', 'rios-y-cascadas', -22.6900, -64.3650, 'Bermejo — Aniceto Arce', 'Balneario natural y cascada en el entorno subtropical de Bermejo.', true],
            ['Reserva Nacional de Flora y Fauna Tariquía', 'reservas-y-lagunas', -22.0500, -64.3000, 'Sector Tariquía — provincias Aniceto Arce y O’Connor', 'Área protegida de bosques nublados y biodiversidad; el ingreso debe coordinarse localmente.', true],
            ['Hotel Eco Tours Tariquía', 'hoteles', -22.7310, -64.3440, 'Bermejo — Aniceto Arce', 'Opción de hospedaje en la ciudad de Bermejo.', false],

            // Provincia Burdet O'Connor
            ['Entre Ríos', 'plazas-y-pueblos', -21.5266, -64.1738, 'Entre Ríos — provincia Burdet O’Connor', 'Capital provincial y puerta de acceso a paisajes subandinos y cultura guaraní.', true],
            ['Plaza principal de Entre Ríos', 'plazas-y-pueblos', -21.5262, -64.1735, 'Centro de Entre Ríos — Burdet O’Connor', 'Centro cívico y punto de partida para conocer la localidad y su gastronomía.', false],
            ['Salinas', 'rios-y-cascadas', -21.6760, -64.1180, 'Salinas, Entre Ríos — Burdet O’Connor', 'Paisaje de serranía, ríos y comunidades rurales del municipio de Entre Ríos.', false],
            ['Narváez', 'rios-y-cascadas', -21.4150, -64.1960, 'Narváez, Entre Ríos — Burdet O’Connor', 'Valle subandino con cursos de agua, producción rural y recorridos de naturaleza.', false],
            ['Mercado de Entre Ríos', 'mercados', -21.5270, -64.1742, 'Centro de Entre Ríos — Burdet O’Connor', 'Punto de encuentro para productos regionales y cocina del subandino tarijeño.', false],
            ['Cultura guaraní de O’Connor', 'museos-y-casas-historicas', -21.5650, -64.1200, 'Comunidades guaraníes de Entre Ríos — Burdet O’Connor', 'Referencia territorial para experiencias culturales que deben coordinarse con las comunidades.', false],

            // Provincia Gran Chaco
            ['Yacuiba', 'plazas-y-pueblos', -22.0164, -63.6776, 'Yacuiba — provincia Gran Chaco', 'Ciudad fronteriza y centro comercial con identidad cultural chaqueña.', true],
            ['Plaza 12 de Agosto de Yacuiba', 'plazas-y-pueblos', -22.0139, -63.6778, 'Centro de Yacuiba — Gran Chaco', 'Plaza urbana para iniciar recorridos por el centro de Yacuiba.', false],
            ['Serranía del Aguaragüe', 'reservas-y-lagunas', -22.0300, -63.7700, 'Serranía del Aguaragüe — Gran Chaco', 'Área montañosa protegida, nacientes de agua y senderos del Chaco tarijeño.', true],
            ['Villa Montes', 'plazas-y-pueblos', -21.2648, -63.4690, 'Villa Montes — provincia Gran Chaco', 'Ciudad a orillas del Pilcomayo, vinculada a la historia de la Guerra del Chaco.', true],
            ['Río Pilcomayo en Villa Montes', 'rios-y-cascadas', -21.2555, -63.4745, 'Costanera de Villa Montes — Gran Chaco', 'Río emblemático del Chaco para contemplación del paisaje y gastronomía de pescado.', true],
            ['Museo Histórico de la Guerra del Chaco', 'museos-y-casas-historicas', -21.2642, -63.4680, 'Villa Montes — Gran Chaco', 'Espacio de memoria histórica relacionado con la Guerra del Chaco.', false],
            ['Mercado Campesino de Yacuiba', 'mercados', -22.0185, -63.6750, 'Yacuiba — Gran Chaco', 'Productos regionales, intercambio fronterizo y sabores de la cocina chaqueña.', false],
            ['Caraparí', 'plazas-y-pueblos', -21.8327, -63.8795, 'Caraparí — provincia Gran Chaco', 'Municipio chaqueño rodeado de serranías, quebradas y comunidades rurales.', false],
            ['Hotel Rancho Olivo', 'hoteles', -21.2656, -63.4684, 'Villa Montes — Gran Chaco', 'Hotel de referencia para visitantes de Villa Montes.', false, '+591 4 6722059', 'http://www.elranchoolivo.com'],
            ['Hotel Las Vegas', 'hoteles', -22.0157, -63.6788, 'Yacuiba — Gran Chaco', 'Hospedaje urbano con restaurante en la ciudad de Yacuiba.', false],
        ];

        $imagesByType = [
            'hoteles' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80',
            'hospedaje-rural' => 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?auto=format&fit=crop&w=900&q=80',
            'comida-tradicional' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=900&q=80',
            'mercados' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?auto=format&fit=crop&w=900&q=80',
            'bodegas-y-vinos' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=900&q=80',
            'museos-y-casas-historicas' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=900&q=80',
            'templos' => 'https://images.unsplash.com/photo-1548625361-9878e2f0b4d4?auto=format&fit=crop&w=900&q=80',
            'plazas-y-pueblos' => 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?auto=format&fit=crop&w=900&q=80',
            'reservas-y-lagunas' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=900&q=80',
            'rios-y-cascadas' => 'https://images.unsplash.com/photo-1432405972618-c60b0225b8f9?auto=format&fit=crop&w=900&q=80',
            'miradores-y-canones' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80',
        ];
        $specificImages = [
            'Casa Dorada' => 'https://cdn.kanootours.com/media/wysiwyg/tarija/casa-doradal-tarija.jpg',
            'Lago San Jacinto' => 'https://cdn.kanootours.com/media/wysiwyg/tarija/san-jacinto-dam-tarija.jpg',
            'Valle de la Concepción' => 'https://cdn.kanootours.com/media/wysiwyg/tarija/conception-wine--tarija.jpg',
            'Lagunas de Tajzara' => 'https://elpais.bo/img/images_1200/contents/2019/08/31/8dce286d-5372-4ade-b649-daa669f6271f.jpg',
        ];

        foreach ($places as $index => $place) {
            [$title, $type, $latitude, $longitude, $address, $summary, $featured] = $place;
            AttractionPlace::updateOrCreate(['slug' => Str::slug($title)], [
                'attraction_type_id' => $types[$type]->id,
                'titulo' => $title,
                'resumen' => $summary,
                'descripcion' => $summary . ' Verifica condiciones de acceso, horarios y disponibilidad antes de viajar.',
                'imagen' => $specificImages[$title] ?? $imagesByType[$type] ?? '/images/referencia/tarija.jpg',
                'latitud' => $latitude,
                'longitud' => $longitude,
                'direccion' => $address,
                'telefono' => $place[7] ?? null,
                'sitio_web' => $place[8] ?? null,
                'horario' => 'Consultar antes de la visita',
                'destacado' => $featured,
                'activo' => true,
                'orden' => $index + 1,
            ]);
        }
    }
}
