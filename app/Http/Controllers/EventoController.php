<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\ProvinciaTuristica;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index(Request $request): View
    {
        $municipality = $request->validate([
            'municipio' => ['nullable', 'string', 'exists:provincias_turisticas,slug'],
        ])['municipio'] ?? null;

        return view('eventos.index', [
            'eventos' => Evento::query()
                ->with(['destino', 'municipio'])
                ->where('activo', true)
                ->when($municipality, fn ($query) => $query->whereHas(
                    'municipio',
                    fn ($municipio) => $municipio->where('slug', $municipality)
                ))
                ->orderBy('fecha_inicio')
                ->paginate(9)
                ->withQueryString(),
            'municipality' => $municipality,
            'municipalities' => ProvinciaTuristica::query()->where('activo', true)->orderBy('orden')->get(['nombre', 'slug']),
        ]);
    }

    public function show(Evento $evento): View
    {
        abort_unless($evento->activo, 404);

        return view('eventos.show', [
            'evento' => $evento->load(['destino', 'municipio']),
        ]);
    }
}
