<?php

namespace App\Http\Controllers;

use App\Models\TourismServiceProvider;
use Illuminate\View\View;

class ServicioTuristicoController extends Controller
{
    public static function servicios(): array
    {
        return [
            'alojamientos' => [
                'provider_type' => 'hospedaje',
                'titulo' => 'Alojamientos',
                'subtitulo' => 'Hoteles, hostales, residenciales y otros hospedajes aprobados.',
                'color' => '#6f1d2c', 'accent' => '#2d0b12', 'sigla' => 'HOTEL', 'icono' => 'fa-hotel',
            ],
            'gastronomia' => [
                'provider_type' => 'gastronomia',
                'titulo' => 'Gastronomía',
                'subtitulo' => 'Restaurantes, cafés, mercados y propuestas gastronómicas aprobadas.',
                'color' => '#8f3f2f', 'accent' => '#4b1d16', 'sigla' => 'SABOR', 'icono' => 'fa-utensils',
            ],
            'agencias-de-viaje' => [
                'provider_type' => 'agencia_viajes',
                'titulo' => 'Agencias de Viaje',
                'subtitulo' => 'Agencias aprobadas para reservas, paquetes y asistencia al visitante.',
                'color' => '#7e3444', 'accent' => '#42121b', 'sigla' => 'VIAJE', 'icono' => 'fa-plane-departure',
            ],
            'operadoras-de-turismo' => [
                'provider_type' => 'operadora_turismo',
                'titulo' => 'Operadoras de Turismo',
                'subtitulo' => 'Operadores aprobados para circuitos, naturaleza y experiencias especializadas.',
                'color' => '#96545d', 'accent' => '#581722', 'sigla' => 'TOUR', 'icono' => 'fa-person-hiking',
            ],
            'guias-departamentales' => [
                'provider_type' => 'guia_departamental',
                'titulo' => 'Guías Departamentales',
                'subtitulo' => 'Guías aprobados con idiomas, especialidades y experiencia publicada.',
                'color' => '#a88c76', 'accent' => '#6f1d2c', 'sigla' => 'GUÍA', 'icono' => 'fa-id-card',
            ],
            'transporte-turistico' => [
                'provider_type' => 'transporte',
                'titulo' => 'Transporte Turístico',
                'subtitulo' => 'Servicios habilitados de traslado y movilidad para visitantes.',
                'color' => '#315c6b', 'accent' => '#17333d', 'sigla' => 'RUTA', 'icono' => 'fa-van-shuttle',
            ],
            'artesania-y-comercio' => [
                'provider_type' => 'artesania_comercio',
                'titulo' => 'Artesanía y Comercio',
                'subtitulo' => 'Artesanos, productores y comercios vinculados al turismo.',
                'color' => '#8a6038', 'accent' => '#49311c', 'sigla' => 'LOCAL', 'icono' => 'fa-shop',
            ],
            'actividades-turisticas' => [
                'provider_type' => 'actividad_turistica',
                'titulo' => 'Actividades Turísticas',
                'subtitulo' => 'Experiencias recreativas, culturales, de aventura y entretenimiento.',
                'color' => '#39705b', 'accent' => '#1c4033', 'sigla' => 'VIVE', 'icono' => 'fa-person-hiking',
            ],
            'otros-servicios' => [
                'provider_type' => 'otro',
                'titulo' => 'Otros Servicios',
                'subtitulo' => 'Prestadores turísticos aprobados de otras especialidades.',
                'color' => '#806958', 'accent' => '#2d0b12', 'sigla' => 'OTROS', 'icono' => 'fa-compass',
            ],
        ];
    }

    public function index(): View
    {
        $services = collect(self::servicios())->map(function (array $service, string $slug) {
            $service['slug'] = $slug;
            $service['providers'] = TourismServiceProvider::query()
                ->where('status', 'approved')
                ->where('provider_type', $service['provider_type'])
                ->with('mapPlace')
                ->orderBy('commercial_name')
                ->get();

            return $service;
        });

        return view('servicios.index', compact('services'));
    }

    public function show(string $servicio): View
    {
        abort_unless(array_key_exists($servicio, self::servicios()), 404);

        $servicioTuristico = self::servicios()[$servicio];
        $servicioTuristico['slug'] = $servicio;
        $providers = TourismServiceProvider::query()
            ->where('status', 'approved')
            ->where('provider_type', $servicioTuristico['provider_type'])
            ->with('mapPlace')
            ->orderBy('municipality')
            ->orderBy('commercial_name')
            ->get();

        return view('servicios.show', compact('servicioTuristico', 'providers'));
    }
}
