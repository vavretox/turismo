<?php

namespace App\Http\Controllers;

use App\Models\WeeklyActivity;
use App\Models\ProvinciaTuristica;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class WeeklyActivityController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'municipio' => ['nullable', 'string', 'exists:provincias_turisticas,slug'],
        ]);
        $from = $filters['desde'] ?? null;
        $to = $filters['hasta'] ?? null;
        $municipality = $filters['municipio'] ?? null;

        $query = WeeklyActivity::query()
            ->with('municipio')
            ->visible()
            ->when($municipality, fn ($builder) => $builder->whereHas(
                'municipio',
                fn ($municipio) => $municipio->where('slug', $municipality)
            ));

        if ($from || $to) {
            $activities = $query
                ->whereNotNull('fecha_actividad')
                ->when($from, fn ($builder) => $builder->whereDate('fecha_actividad', '>=', $from))
                ->when($to, fn ($builder) => $builder->whereDate('fecha_actividad', '<=', $to))
                ->orderBy('fecha_actividad')
                ->orderBy('orden')
                ->get();
        } else {
            $today = now()->startOfDay();
            $visibleActivities = $query->orderBy('orden')->get();

            $activities = $visibleActivities
                ->filter(fn (WeeklyActivity $activity) => $activity->fecha_actividad?->gte($today))
                ->sortBy('fecha_actividad')
                ->concat(
                    $visibleActivities
                        ->filter(fn (WeeklyActivity $activity) => $activity->fecha_actividad?->lt($today))
                        ->sortByDesc('fecha_actividad')
                )
                ->concat(
                    $visibleActivities->filter(fn (WeeklyActivity $activity) => is_null($activity->fecha_actividad))
                )
                ->values();
        }

        return view('actividades.index', [
            'activities' => $activities,
            'from' => $from,
            'to' => $to,
            'municipality' => $municipality,
            'municipalities' => ProvinciaTuristica::query()->where('activo', true)->orderBy('orden')->get(['nombre', 'slug']),
        ]);
    }

    public function show(WeeklyActivity $actividad): View
    {
        abort_unless($actividad->activo, 404);

        return view('actividades.show', [
            'actividad' => $actividad->load('municipio'),
            'otrasActividades' => WeeklyActivity::query()
                ->with('municipio')
                ->visible()
                ->whereKeyNot($actividad->getKey())
                ->orderBy('orden')
                ->orderBy('fecha_actividad')
                ->get(),
        ]);
    }
}
