<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Evento;
use App\Models\Noticia;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));

        return view('buscar.index', [
            'query' => $query,
            'destinos' => $this->destinos($query),
            'eventos' => $this->eventos($query),
            'noticias' => $this->noticias($query),
        ]);
    }

    private function destinos(string $query)
    {
        return Destino::query()
            ->with(['categoria', 'municipio'])
            ->where('activo', true)
            ->when($query !== '', fn ($builder) => $builder
                ->where(fn ($builder) => $builder
                    ->where('nombre', 'like', "%{$query}%")
                    ->orWhere('resumen', 'like', "%{$query}%")
                    ->orWhere('ubicacion', 'like', "%{$query}%")
                    ->orWhereHas('municipio', fn ($municipio) => $municipio
                        ->where('nombre', 'like', "%{$query}%")
                        ->orWhere('provincia', 'like', "%{$query}%"))))
            ->orderBy('orden')
            ->take(6)
            ->get();
    }

    private function eventos(string $query)
    {
        return Evento::query()
            ->with(['destino', 'municipio'])
            ->where('activo', true)
            ->when($query !== '', fn ($builder) => $builder
                ->where(fn ($builder) => $builder
                    ->where('titulo', 'like', "%{$query}%")
                    ->orWhere('descripcion', 'like', "%{$query}%")
                    ->orWhere('lugar', 'like', "%{$query}%")
                    ->orWhereHas('municipio', fn ($municipio) => $municipio
                        ->where('nombre', 'like', "%{$query}%")
                        ->orWhere('provincia', 'like', "%{$query}%"))))
            ->orderBy('fecha_inicio')
            ->take(6)
            ->get();
    }

    private function noticias(string $query)
    {
        return Noticia::query()
            ->with('destino')
            ->where('activo', true)
            ->when($query !== '', fn ($builder) => $builder
                ->where(fn ($builder) => $builder
                    ->where('titulo', 'like', "%{$query}%")
                    ->orWhere('resumen', 'like', "%{$query}%")
                    ->orWhere('contenido', 'like', "%{$query}%")))
            ->latest('publicado_en')
            ->take(6)
            ->get();
    }
}
