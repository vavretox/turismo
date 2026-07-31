<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class NoticiaController extends Controller
{
    public function index(Request $request): View
    {
        $requestedWeek = $request->string('semana')->toString();

        try {
            $weekStart = $requestedWeek !== ''
                ? CarbonImmutable::createFromFormat('Y-m-d', $requestedWeek)->startOfWeek()
                : CarbonImmutable::now()->startOfWeek();
        } catch (\Throwable) {
            $weekStart = CarbonImmutable::now()->startOfWeek();
        }

        $weekEnd = $weekStart->endOfWeek();

        return view('noticias.index', [
            'noticias' => Noticia::query()
                ->with('destino')
                ->where('activo', true)
                ->whereBetween('publicado_en', [$weekStart->startOfDay(), $weekEnd->endOfDay()])
                ->latest('publicado_en')
                ->paginate(9)
                ->withQueryString(),
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'previousWeek' => $weekStart->subWeek()->format('Y-m-d'),
            'nextWeek' => $weekStart->addWeek()->format('Y-m-d'),
            'isCurrentWeek' => $weekStart->isSameDay(CarbonImmutable::now()->startOfWeek()),
        ]);
    }

    public function show(Noticia $noticia): View
    {
        abort_unless($noticia->activo, 404);

        return view('noticias.show', [
            'noticia' => $noticia->load('destino'),
        ]);
    }
}
