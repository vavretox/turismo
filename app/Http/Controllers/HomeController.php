<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Evento;
use App\Models\Noticia;
use App\Models\PortalImage;
use App\Models\ProvinciaTuristica;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'destinos' => Destino::query()->with('municipio')->where('activo', true)->where('destacado', true)->orderBy('orden')->take(6)->get(),
            'eventos' => Evento::query()->where('activo', true)->latest('fecha_inicio')->take(3)->get(),
            'noticias' => Noticia::query()->where('activo', true)->latest('publicado_en')->take(3)->get(),
            'heroImages' => PortalImage::query()
                ->where('clave', 'like', 'home_hero_%')
                ->where('activo', true)
                ->orderBy('clave')
                ->take(5)
                ->get(),
            'serviciosTuristicos' => ServicioTuristicoController::servicios(),
            'municipiosTuristicos' => ProvinciaTuristica::query()->where('activo', true)->orderBy('orden')->take(6)->get(),
        ]);
    }
}
