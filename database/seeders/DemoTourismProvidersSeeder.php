<?php

namespace Database\Seeders;

use App\Models\AttractionPlace;
use App\Models\AttractionType;
use App\Models\TourismServiceProvider;
use App\Models\User;
use App\Models\ProviderOffering;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoTourismProvidersSeeder extends Seeder
{
    public const PASSWORD = 'TurismoDemo2026!';

    public function run(): void
    {
        User::query()->where('email', 'movilidad.gualdalquivir@demo.example.com')
            ->update(['email' => 'movilidad.guadalquivir@demo.example.com']);

        $types = AttractionType::query()->where('activo', true)->get();
        $fallbackType = $types->firstOrFail();

        foreach ($this->providers() as $demo) {
            $user = User::query()->updateOrCreate(
                ['email' => $demo['email']],
                ['name' => $demo['representative'], 'password' => self::PASSWORD, 'role' => 'provider', 'is_admin' => false, 'admin_sections' => []],
            );

            $provider = TourismServiceProvider::withoutEvents(fn () => TourismServiceProvider::query()->updateOrCreate(
                ['commercial_name' => $demo['name']],
                [
                    'user_id' => $user->id,
                    'provider_type' => $demo['provider_type'],
                    'provider_type_other' => null,
                    'business_name' => $demo['name'].' S.R.L.',
                    'nit' => $demo['nit'],
                    'has_tourism_license' => true,
                    'tourism_license_issued_at' => now()->subYears(2)->toDateString(),
                    'tourism_license_renewed_at' => now()->addYear()->toDateString(),
                    'legal_representative' => $demo['representative'],
                    'identity_document' => $demo['identity'],
                    'landline' => '466'.$demo['phone_suffix'],
                    'whatsapp' => '729'.$demo['phone_suffix'],
                    'email' => $demo['email'],
                    'website' => 'https://example.com/'.$demo['slug'],
                    'facebook' => 'https://facebook.com/'.$demo['slug'],
                    'instagram' => 'https://instagram.com/'.$demo['slug'],
                    'tiktok' => 'https://tiktok.com/@'.$demo['slug'],
                    'other_social_network' => 'https://x.com/'.$demo['slug'],
                    'department' => 'Tarija',
                    'municipality' => $demo['municipality'],
                    'address' => $demo['address'],
                    'maps_location' => $demo['lat'].','.$demo['lng'],
                    'lodging_type' => $demo['provider_type'] === 'hospedaje' ? 'hotel' : null,
                    'room_count' => $demo['provider_type'] === 'hospedaje' ? 28 : null,
                    'guest_capacity' => $demo['provider_type'] === 'hospedaje' ? 65 : null,
                    'lodging_services' => $demo['provider_type'] === 'hospedaje' ? ['wifi', 'desayuno', 'estacionamiento', 'accesibilidad'] : null,
                    'agency_type' => $demo['provider_type'] === 'agencia_viajes' ? 'receptiva' : null,
                    'agency_services' => $demo['provider_type'] === 'agencia_viajes' ? ['boletos', 'hoteles', 'transporte', 'seguros'] : null,
                    'tourism_modalities' => in_array($demo['provider_type'], ['operadora_turismo', 'actividad_turistica'], true) ? ['cultural', 'naturaleza', 'aventura', 'gastronomico'] : null,
                    'package_types' => in_array($demo['provider_type'], ['agencia_viajes', 'operadora_turismo'], true) ? 'City tours, rutas del vino y circuitos departamentales' : null,
                    'main_destinations' => 'Tarija, San Lorenzo, Valle de la Concepción y Reserva de Sama',
                    'has_guide_credential' => $demo['provider_type'] === 'guia_departamental',
                    'guide_credential_issued_at' => $demo['provider_type'] === 'guia_departamental' ? now()->subYear()->toDateString() : null,
                    'guide_credential_renewed_at' => $demo['provider_type'] === 'guia_departamental' ? now()->addYear()->toDateString() : null,
                    'languages' => $demo['provider_type'] === 'guia_departamental' ? ['Español', 'Inglés', 'Portugués'] : null,
                    'specialties' => $demo['provider_type'] === 'guia_departamental' ? ['Cultura', 'Naturaleza', 'Enoturismo'] : null,
                    'experience_years' => 8,
                    'documents' => [],
                    'declaration_accepted' => true,
                    'applicant_name' => $demo['representative'],
                    'application_place' => 'Tarija',
                    'application_date' => now()->subMonth()->toDateString(),
                    'status' => 'approved',
                    'admin_notes' => 'Prestador ficticio habilitado exclusivamente para pruebas del portal.',
                ],
            ));

            $mapType = $types->first(fn ($type) => Str::contains(Str::lower($type->nombre), $demo['map_keyword'])) ?? $fallbackType;
            $place = AttractionPlace::query()->updateOrCreate(
                ['tourism_service_provider_id' => $provider->id],
                [
                    'attraction_type_id' => $mapType->id,
                    'titulo' => $demo['name'],
                    'resumen' => $demo['summary'],
                    'descripcion' => $demo['description'],
                    'imagen' => $demo['image'],
                    'galeria' => $demo['gallery'],
                    'latitud' => $demo['lat'],
                    'longitud' => $demo['lng'],
                    'direccion' => $demo['address'].', '.$demo['municipality'],
                    'telefono' => '729'.$demo['phone_suffix'],
                    'whatsapp' => '591729'.$demo['phone_suffix'],
                    'sitio_web' => 'https://example.com/'.$demo['slug'],
                    'facebook' => 'https://facebook.com/'.$demo['slug'],
                    'instagram' => 'https://instagram.com/'.$demo['slug'],
                    'tiktok' => 'https://tiktok.com/@'.$demo['slug'],
                    'x_url' => 'https://x.com/'.$demo['slug'],
                    'horario' => 'Lunes a domingo, 08:00 a 22:00',
                    'precio' => $demo['price'],
                    'room_options' => $demo['provider_type'] === 'hospedaje' ? ['Individual', 'Matrimonial', 'Familiar', 'Suite'] : null,
                    'service_details' => ['detail_1' => $demo['detail_1'], 'detail_2' => $demo['detail_2'], 'detail_3' => $demo['detail_3']],
                    'destacado' => false,
                    'activo' => true,
                    'orden' => 50,
                ],
            );

            TourismServiceProvider::withoutEvents(fn () => $provider->update(['attraction_place_id' => $place->id]));

            $offeringTitle = match ($demo['provider_type']) {
                'hospedaje' => 'Suite chapaca con desayuno',
                'gastronomia' => 'Menú degustación sabores de Tarija',
                'agencia_viajes' => 'Escapada a Tarija — 3 días',
                'operadora_turismo' => 'Ruta del vino y singani — día completo',
                'guia_departamental' => 'Recorrido histórico por la ciudad',
                'transporte' => 'Traslado aeropuerto — centro',
                'artesania_comercio' => 'Colección artesanal chapaca',
                'actividad_turistica' => 'Ciclismo entre viñedos',
                default => 'Experiencia cultural de la vendimia',
            };
            ProviderOffering::query()->updateOrCreate(
                ['tourism_service_provider_id' => $provider->id, 'titulo' => $offeringTitle],
                [
                    'resumen' => 'Una propuesta demostrativa con información clara para ayudar al turista a elegir y reservar.',
                    'descripcion' => 'Contenido ficticio preparado para probar la publicación especializada de cada tipo de prestador.',
                    'imagen' => $demo['image'],
                    'galeria' => array_slice($demo['gallery'], 0, 2),
                    'duracion' => in_array($demo['provider_type'], ['artesania_comercio', 'hospedaje'], true) ? 'Según disponibilidad' : 'Día completo',
                    'precio' => $demo['price'],
                    'incluye' => $demo['detail_2']."\n".$demo['detail_3'],
                    'activo' => true,
                    'orden' => 1,
                ],
            );
        }
    }

    private function providers(): array
    {
        $gallery = [
            'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=1200&q=80',
            'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80',
        ];

        return [
            $this->demo('hospedaje', 'Hotel Mirador Chapaco', 'hotel.mirador@demo.example.com', 'hotel-mirador-chapaco', 'Mariana López', 'TJA-1001', '0001', 'Tarija', 'Av. Las Américas 450', -21.5312, -64.7284, 'hotel', 'Un hotel confortable cerca del centro de Tarija.', 'Habitaciones cómodas, desayuno regional y atención durante todo el día.', 'Hotel boutique de 3 estrellas', 'Wi-Fi, desayuno, estacionamiento y accesibilidad', '28 habitaciones y capacidad para 65 huéspedes', 'Desde Bs 280', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('gastronomia', 'Sabores de la Campiña', 'sabores.campina@demo.example.com', 'sabores-campina', 'Diego Romero', 'TJA-1002', '0002', 'Tarija', 'Calle Sucre 215', -21.5344, -64.7310, 'gastr', 'Cocina tarijeña preparada con productos locales.', 'Restaurante familiar con platos regionales, vinos de altura y opciones vegetarianas.', 'Cocina regional tarijeña', 'Saice, costillitas, empanadas blanqueadas y vinos locales', 'Reservas para grupos', 'Desde Bs 45', 'https://images.unsplash.com/photo-1515003197210-e0cd71810b5f?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('agencia_viajes', 'Tarija Viajes y Rutas', 'viajes.rutas@demo.example.com', 'tarija-viajes-rutas', 'Carla Méndez', 'TJA-1003', '0003', 'Tarija', 'Calle General Trigo 330', -21.5360, -64.7301, 'agencia', 'Reservas, paquetes y asistencia para recorrer Tarija.', 'Agencia receptiva especializada en viajes personalizados y circuitos departamentales.', 'Agencia receptiva', 'Boletos, hoteles, transporte y seguros', 'Atención a viajeros nacionales y extranjeros', 'Consultar', 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('operadora_turismo', 'Explora Tarija Operadora', 'explora.operadora@demo.example.com', 'explora-tarija-operadora', 'Luis Gutiérrez', 'TJA-1004', '0004', 'San Lorenzo', 'Plaza principal, acera norte', -21.4185, -64.7483, 'oper', 'Circuitos de naturaleza, cultura y enoturismo.', 'Operadora con recorridos guiados por municipios, bodegas y reservas naturales.', 'Naturaleza y aventura', 'Rutas del vino, trekking y cultura viva', 'Cobertura departamental', 'Desde Bs 160', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('guia_departamental', 'Valeria Cruz Guía Turística', 'valeria.guia@demo.example.com', 'valeria-cruz-guia', 'Valeria Cruz', 'TJA-1005', '0005', 'Tarija', 'Barrio El Molino', -21.5298, -64.7350, 'gu', 'Guía acreditada para experiencias culturales y naturales.', 'Acompañamiento en español, inglés y portugués para viajeros individuales y grupos.', 'Español, Inglés y Portugués', 'Cultura, naturaleza y enoturismo', 'Tarija, San Lorenzo, Uriondo y Sama', 'Desde Bs 120', 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('transporte', 'Movilidad Turística Guadalquivir', 'movilidad.guadalquivir@demo.example.com', 'movilidad-turistica-guadalquivir', 'Óscar Vargas', 'TJA-1006', '0006', 'Tarija', 'Terminal de buses, oficina 12', -21.5450, -64.7232, 'trans', 'Traslados turísticos seguros dentro y fuera de la ciudad.', 'Servicio de vehículos con conductor para aeropuerto, bodegas y circuitos departamentales.', 'Vagonetas y minibuses', 'Traslados privados, aeropuerto y rutas turísticas', 'Cobertura departamental', 'Desde Bs 80', 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('artesania_comercio', 'Manos Chapacas Artesanías', 'manos.chapacas@demo.example.com', 'manos-chapacas-artesanias', 'Rosa Flores', 'TJA-1007', '0007', 'San Lorenzo', 'Calle Eustaquio Méndez 120', -21.4177, -64.7472, 'art', 'Artesanía local elaborada por productores tarijeños.', 'Tienda y taller de tejidos, cuero, cerámica y recuerdos con identidad chapaca.', 'Artesanía y producción local', 'Tejidos, cuero, cerámica y recuerdos', 'Ventas unitarias y para grupos', 'Desde Bs 25', 'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('actividad_turistica', 'Aventura Valle Central', 'aventura.valle@demo.example.com', 'aventura-valle-central', 'Pablo Ríos', 'TJA-1008', '0008', 'Uriondo', 'Valle de la Concepción, zona central', -21.6920, -64.6565, 'activ', 'Experiencias recreativas entre viñedos y paisajes del valle.', 'Ciclismo, caminatas interpretativas y actividades al aire libre con equipos incluidos.', 'Aventura suave y recreación', 'Ciclismo, senderismo y experiencias entre viñedos', 'Grupos de 2 a 15 personas', 'Desde Bs 140', 'https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1600&q=85', $gallery),
            $this->demo('otro', 'Centro Cultural La Vendimia', 'centro.vendimia@demo.example.com', 'centro-cultural-vendimia', 'Natalia Ávila', 'TJA-1009', '0009', 'Uriondo', 'Calle de la Vendimia 88', -21.6950, -64.6581, 'cultur', 'Espacio cultural para conocer tradiciones del valle.', 'Exposiciones, talleres, degustaciones y encuentros con productores locales.', 'Centro cultural turístico', 'Talleres, exposiciones y degustaciones', 'Actividades con reserva previa', 'Desde Bs 30', 'https://images.unsplash.com/photo-1564399579883-451a5d44ec08?auto=format&fit=crop&w=1600&q=85', $gallery),
        ];
    }

    private function demo(string $providerType, string $name, string $email, string $slug, string $representative, string $identity, string $phoneSuffix, string $municipality, string $address, float $lat, float $lng, string $mapKeyword, string $summary, string $description, string $detail1, string $detail2, string $detail3, string $price, string $image, array $gallery): array
    {
        return compact('providerType', 'name', 'email', 'slug', 'representative', 'identity', 'phoneSuffix', 'municipality', 'address', 'lat', 'lng', 'mapKeyword', 'summary', 'description', 'detail1', 'detail2', 'detail3', 'price', 'image', 'gallery') + [
            'provider_type' => $providerType,
            'phone_suffix' => $phoneSuffix,
            'map_keyword' => $mapKeyword,
            'detail_1' => $detail1,
            'detail_2' => $detail2,
            'detail_3' => $detail3,
            'nit' => '901'.$phoneSuffix.'01',
        ];
    }
}
