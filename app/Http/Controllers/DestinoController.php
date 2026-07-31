<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use Illuminate\Contracts\View\View;

class DestinoController extends Controller
{
    public function index(): View
    {
        return view('destinos.index', [
            'destinos' => Destino::query()
                ->with(['categoria', 'municipio'])
                ->where('activo', true)
                ->orderBy('orden')
                ->paginate(9),
        ]);
    }

    public function show(Destino $destino): View
    {
        abort_unless($destino->activo, 404);

        return view('destinos.show', [
            'destino' => $destino->load(['categoria', 'municipio', 'eventos', 'noticias']),
            'destinosRelacionados' => Destino::query()
                ->with(['categoria', 'municipio'])
                ->where('activo', true)
                ->whereKeyNot($destino->getKey())
                ->when($destino->categoria_id, fn ($query) => $query->where('categoria_id', $destino->categoria_id))
                ->orderByDesc('destacado')
                ->orderBy('orden')
                ->limit(3)
                ->get(),
        ]);
    }
}
