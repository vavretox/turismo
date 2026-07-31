<?php

namespace App\Http\Controllers;

use App\Models\ProvinciaTuristica;
use Illuminate\Contracts\View\View;

class ProvinciaTuristicaController extends Controller
{
    public function index(): View
    {
        return view('provincias.index', [
            'municipios' => ProvinciaTuristica::query()
                ->where('activo', true)
                ->orderBy('orden')
                ->orderBy('nombre')
                ->take(11)
                ->get(),
        ]);
    }

    public function show(ProvinciaTuristica $municipio): View
    {
        abort_unless($municipio->activo, 404);

        return view('provincias.show', [
            'municipio' => $municipio,
        ]);
    }
}
