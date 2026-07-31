<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\Evento;
use App\Models\Noticia;
use App\Models\ProvinciaTuristica;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourismChatController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'min:2', 'max:500']]);
        $question = Str::lower($data['message']);
        $terms = collect(preg_split('/\s+/u', $question))
            ->map(fn ($term) => trim($term, "¿?¡!.,;:\"'()"))
            ->filter(fn ($term) => mb_strlen($term) >= 4)
            ->reject(fn ($term) => in_array($term, ['para', 'sobre', 'donde', 'cuando', 'como', 'quiero', 'puedo', 'turismo', 'tarija'], true))
            ->unique()->take(6)->values();

        $search = function (Builder $query, array $columns) use ($terms): Builder {
            return $query->where(function (Builder $builder) use ($terms, $columns): void {
                foreach ($terms as $term) {
                    $builder->orWhere(function (Builder $nested) use ($term, $columns): void {
                        foreach ($columns as $column) {
                            $nested->orWhere($column, 'like', "%{$term}%");
                        }
                    });
                }
            });
        };

        if ($terms->isEmpty()) {
            return response()->json([
                'answer' => 'Puedo ayudarte con destinos, provincias, bodegas, naturaleza, próximos eventos y noticias turísticas. Prueba preguntando: “¿Qué puedo visitar en Uriondo?”',
                'results' => [],
            ]);
        }

        $results = collect();
        if (Str::contains($question, ['evento', 'agenda', 'actividad'])) {
            Evento::query()->where('activo', true)->orderBy('fecha_inicio')->take(3)->get()
                ->each(fn ($item) => $results->push(['type' => 'Evento', 'title' => $item->titulo, 'summary' => trim(($item->lugar ? $item->lugar.'. ' : '').Str::limit($item->descripcion, 130)), 'url' => route('eventos.show', $item)]));
        }
        if (Str::contains($question, ['noticia', 'novedad'])) {
            Noticia::query()->where('activo', true)->latest('publicado_en')->take(3)->get()
                ->each(fn ($item) => $results->push(['type' => 'Noticia', 'title' => $item->titulo, 'summary' => $item->resumen, 'url' => route('noticias.show', $item)]));
        }
        $search(Destino::query()->where('activo', true), ['nombre', 'resumen', 'descripcion', 'ubicacion'])
            ->take(3)->get()->each(fn ($item) => $results->push(['type' => 'Destino', 'title' => $item->nombre, 'summary' => $item->resumen, 'url' => route('destinos.show', $item)]));
        $search(ProvinciaTuristica::query()->where('activo', true), ['nombre', 'provincia', 'subtitulo', 'resumen', 'descripcion', 'atractivos'])
            ->take(3)->get()->each(fn ($item) => $results->push(['type' => 'Municipio', 'title' => $item->nombre, 'summary' => $item->resumen ?: $item->subtitulo, 'url' => route('municipios.show', $item)]));
        $search(Evento::query()->where('activo', true), ['titulo', 'descripcion', 'lugar'])
            ->take(2)->get()->each(fn ($item) => $results->push(['type' => 'Evento', 'title' => $item->titulo, 'summary' => trim(($item->lugar ? $item->lugar.'. ' : '').Str::limit($item->descripcion, 130)), 'url' => route('eventos.show', $item)]));
        $search(Noticia::query()->where('activo', true), ['titulo', 'resumen', 'contenido'])
            ->take(2)->get()->each(fn ($item) => $results->push(['type' => 'Noticia', 'title' => $item->titulo, 'summary' => $item->resumen, 'url' => route('noticias.show', $item)]));

        $results = $results->unique('url')->take(5)->values();
        $fallbackAnswer = $results->isEmpty()
            ? 'No encontré información publicada que coincida exactamente. Puedes preguntarme por una provincia, un destino, una bodega, un evento o una actividad específica.'
            : 'Encontré esta información en nuestro portal. Selecciona una opción para conocer todos los detalles:';

        return response()->json([
            'answer' => $fallbackAnswer,
            'results' => $results,
        ]);
    }
}
