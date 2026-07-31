<?php

namespace App\Http\Controllers;

use App\Models\AttractionPlace;
use App\Models\ProviderOffering;
use App\Models\ProvinciaTuristica;
use App\Models\Evento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenerateItineraryController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (! $request->has('destinations') && $request->filled('destination')) {
            $request->merge(['destinations' => [$request->string('destination')->toString()]]);
        }
        if (! $request->has('months') && $request->filled('month')) {
            $request->merge(['months' => [$request->string('month')->toString()]]);
        }

        $data = $request->validate([
            'destinations' => ['required', 'array', 'min:1'],
            'destinations.*' => ['required', 'string', 'max:100', 'distinct', 'exists:provincias_turisticas,nombre'],
            'months' => ['nullable', 'array', 'max:2'],
            'months.*' => ['string', 'in:Enero,Febrero,Marzo,Abril,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre', 'distinct'],
            'startDate' => ['nullable', 'date'],
            'duration' => ['required', 'integer', 'min:1', 'max:7'],
            'companion' => ['required', 'in:solo,pareja,familia,amigos'],
            'roomPreference' => ['nullable', 'in:Individual,Matrimonial,Dos camas,Familiar,Habitaciones múltiples'],
            'pace' => ['required', 'in:tranquilo,activo,intenso'],
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => ['integer'],
            'hotelId' => ['nullable', 'integer'],
            'hotelIds' => ['nullable', 'array'],
            'hotelIds.*' => ['nullable', 'integer', 'exists:attraction_places,id'],
            'budget' => ['required', 'in:economico,razonable,sin-limite'],
        ]);
        $data['roomPreference'] ??= match ($data['companion']) {
            'solo' => 'Individual',
            'familia' => 'Familiar',
            'amigos' => 'Dos camas',
            default => 'Matrimonial',
        };

        $places = AttractionPlace::query()
            ->where('activo', true)
            ->with('type.parent')
            ->orderByDesc('destacado')
            ->orderBy('orden')
            ->get()
            ->reject(fn (AttractionPlace $place) => $place->type?->parent?->slug === 'alojamiento' || $place->type?->slug === 'alojamiento');

        $municipalities = ProvinciaTuristica::query()
            ->where('activo', true)
            ->whereIn('nombre', $data['destinations'])
            ->get(['id', 'nombre', 'provincia', 'fiestas']);

        $destinationPlaces = $places->filter(function (AttractionPlace $place) use ($municipalities) {
            $address = mb_strtolower($place->direccion ?? '');

            return $municipalities->contains(fn (ProvinciaTuristica $municipality) => str_contains(
                $address,
                mb_strtolower($municipality->nombre)
            ));
        });
        if ($destinationPlaces->isNotEmpty()) {
            $places = $destinationPlaces;
        }

        $placeData = $places->take(35)->map(fn (AttractionPlace $place) => [
            'id' => $place->id,
            'title' => $place->titulo,
            'summary' => $place->resumen,
            'address' => $place->direccion,
            'type' => $place->type?->parent?->nombre ?: $place->type?->nombre,
            'subtype' => $place->type?->nombre,
            'type_id' => $place->type?->parent_id ?: $place->attraction_type_id,
            'featured' => (bool) $place->destacado,
            'lat' => (float) $place->latitud,
            'lng' => (float) $place->longitud,
            'search_text' => mb_strtolower(implode(' ', [$place->titulo, $place->resumen, $place->descripcion, $place->type?->nombre])),
            'municipality' => $municipalities->first(fn (ProvinciaTuristica $municipality) => str_contains(
                mb_strtolower($place->direccion ?? ''),
                mb_strtolower($municipality->nombre)
            ))?->nombre,
        ])->values();

        $plan = $this->buildSmartPlan($data, $placeData->all());

        $placesById = $places->keyBy('id');
        $used = [];
        $itinerary = collect($plan['days'] ?? [])->take($data['duration'])->map(function (array $day, int $index) use ($placesById, &$used, $data) {
            $dayPlaces = collect($day['place_ids'] ?? [])
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $placesById->has($id) && ! in_array($id, $used, true))
                ->map(function ($id) use ($placesById, &$used) {
                    $used[] = $id;
                    $place = $placesById->get($id);

                    return [
                        'id' => $place->id,
                        'title' => $place->titulo,
                        'summary' => $place->resumen,
                        'image' => $place->imagen_url,
                        'address' => $place->direccion,
                        'lat' => (float) $place->latitud,
                        'lng' => (float) $place->longitud,
                        'subtype' => $place->type?->nombre,
                    ];
                })->values();

            return [
                'day' => $index + 1,
                'municipality' => $day['municipality'] ?? null,
                'title' => $day['title'] ?? 'Descubre '.implode(', ', $data['destinations']),
                'description' => $day['description'] ?? '',
                'places' => $dayPlaces,
            ];
        })->filter(fn (array $day) => $day['places']->isNotEmpty())->values();

        if ($itinerary->isEmpty()) {
            return response()->json(['message' => 'No hay suficientes lugares publicados para crear este itinerario.'], 422);
        }

        $selectedHotelIds = collect($data['hotelIds'] ?? [])
            ->when(empty($data['hotelIds']) && ! empty($data['hotelId']), fn ($items) => $items->put($data['destinations'][0], $data['hotelId']))
            ->filter();
        $selectedHotels = AttractionPlace::query()
            ->where('activo', true)
            ->whereIn('id', $selectedHotelIds->values())
            ->with('type.parent')
            ->get()
            ->filter(fn (AttractionPlace $hotel) => $hotel->type?->parent?->slug === 'alojamiento' || $hotel->type?->slug === 'alojamiento')
            ->mapWithKeys(function (AttractionPlace $hotel) use ($selectedHotelIds) {
                $municipality = $selectedHotelIds->search($hotel->id, true);
                if (! is_string($municipality) || ! str_contains(mb_strtolower($hotel->direccion ?? ''), mb_strtolower($municipality))) {
                    return [];
                }

                return [$municipality => [
                    'id' => $hotel->id,
                    'title' => $hotel->titulo,
                    'address' => $hotel->direccion,
                    'lat' => (float) $hotel->latitud,
                    'lng' => (float) $hotel->longitud,
                ]];
            });

        return response()->json([
            'introduction' => $plan['introduction'] ?? 'Preparamos una ruta personalizada con lugares publicados en el portal.',
            'itinerary' => $itinerary,
            'travelContext' => $this->buildTravelContext($data, $municipalities, $places),
            'hotels' => $selectedHotels,
            'routeStarts' => $this->buildRouteStarts($municipalities),
            'packages' => $this->destinationPackages($municipalities),
        ]);
    }

    private function destinationPackages($municipalities): array
    {
        $destinationIds = $municipalities->pluck('id')->map(fn ($id) => (int) $id);

        return ProviderOffering::query()
            ->where('activo', true)
            ->whereHas('provider', fn ($query) => $query
                ->where('status', 'approved')
                ->where('provider_type', 'operadora_turismo'))
            ->with('provider.mapPlace')
            ->orderBy('orden')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (ProviderOffering $offering) => collect($offering->destination_ids)
                ->map(fn ($id) => (int) $id)
                ->intersect($destinationIds)
                ->isNotEmpty())
            ->take(8)
            ->map(function (ProviderOffering $offering) use ($municipalities) {
                $offeringDestinationIds = collect($offering->destination_ids)->map(fn ($id) => (int) $id);
                $place = $offering->provider->mapPlace;

                return [
                    'id' => $offering->id,
                    'title' => $offering->titulo,
                    'summary' => $offering->resumen,
                    'image' => $offering->imagen_url,
                    'duration' => $offering->duracion,
                    'price' => $offering->precio,
                    'includes' => $offering->incluye,
                    'provider' => $offering->provider->commercial_name,
                    'destinations' => $municipalities
                        ->whereIn('id', $offeringDestinationIds)
                        ->pluck('nombre')
                        ->values()
                        ->all(),
                    'url' => $place ? route('lugares.show', $place) : null,
                ];
            })
            ->values()
            ->all();
    }

    private function buildSmartPlan(array $preferences, array $places): array
    {
        $destinations = implode(', ', $preferences['destinations']);
        $perDay = match ($preferences['pace']) {
            'intenso' => 4,
            'activo' => 3,
            default => 2,
        };
        $interests = collect($preferences['interests'])->map(fn ($id) => (int) $id);
        $ranked = collect($places)
            ->map(function (array $place) use ($interests, $preferences) {
                $isWalking = str_contains($place['search_text'], 'camin')
                    || str_contains($place['search_text'], 'sender')
                    || str_contains($place['search_text'], 'trek')
                    || str_contains($place['search_text'], 'monta')
                    || str_contains($place['search_text'], 'cascada');
                $place['score'] = ($place['featured'] ? 5 : 0)
                    + ($interests->contains((int) $place['type_id']) ? 10 : 0)
                    + ($place['summary'] ? 1 : 0)
                    + ($preferences['pace'] === 'intenso' && $isWalking ? 16 : 0)
                    + ($preferences['pace'] === 'activo' && $isWalking ? 7 : 0)
                    - ($preferences['pace'] === 'tranquilo' && $isWalking ? 3 : 0);
                $place['is_walking'] = $isWalking;

                return $place;
            })
            ->sortByDesc('score')
            ->values();

        $groups = $ranked
            ->filter(fn (array $place) => filled($place['municipality']))
            ->groupBy('municipality')
            ->map(fn ($items) => $items->values());
        $orderedMunicipalities = collect($preferences['destinations'])
            ->filter(fn (string $municipality) => $groups->has($municipality))
            ->values();
        $includedMunicipalities = $orderedMunicipalities->take($preferences['duration']);
        $omittedMunicipalities = $orderedMunicipalities->slice($preferences['duration'])->values();

        $allocations = $includedMunicipalities->mapWithKeys(fn (string $municipality) => [$municipality => 1]);
        $remainingDays = $preferences['duration'] - $includedMunicipalities->count();
        $allocationIndex = 0;
        while ($remainingDays > 0 && $includedMunicipalities->isNotEmpty()) {
            $municipality = $includedMunicipalities[$allocationIndex % $includedMunicipalities->count()];
            $allocations->put($municipality, $allocations->get($municipality, 0) + 1);
            $allocationIndex++;
            $remainingDays--;
        }

        $dayPlans = collect();
        $dayNumber = 1;
        $previousAnchor = null;
        foreach ($includedMunicipalities as $municipality) {
            $municipalityPlaces = $groups->get($municipality, collect());
            $anchor = null;
            if ($municipalityPlaces->isNotEmpty()) {
                $anchor = $municipalityPlaces->first();
                $municipalityPlaces = $municipalityPlaces->sortBy(fn (array $place) => $this->distanceKm(
                    $anchor['lat'], $anchor['lng'], $place['lat'], $place['lng']
                ) - ($place['score'] * 0.35))->values();
            }

            $transferKm = $previousAnchor && $anchor
                ? $this->distanceKm($previousAnchor['lat'], $previousAnchor['lng'], $anchor['lat'], $anchor['lng'])
                : 0;
            $placeOffset = 0;
            for ($localDay = 0; $localDay < $allocations[$municipality]; $localDay++) {
                $dailyCapacity = $localDay === 0 && $transferKm >= 80
                    ? max(1, $perDay - 1)
                    : $perDay;
                $placeIds = $municipalityPlaces->slice($placeOffset, $dailyCapacity)->pluck('id')->values()->all();
                $placeOffset += $dailyCapacity;
                if (empty($placeIds)) {
                    continue;
                }
                $activityDescription = $preferences['pace'] === 'intenso'
                    ? 'con prioridad para caminatas, senderos y experiencias activas'
                    : 'manteniendo las visitas próximas entre sí para reducir traslados';
                $dayPlans->push([
                    'municipality' => $municipality,
                    'title' => 'Día '.$dayNumber.' en '.$municipality,
                    'description' => ($localDay === 0 && $transferKm >= 80
                        ? 'Traslado territorial de aproximadamente '.round($transferKm).' km hacia '.$municipality.'; por ello se reducen las visitas de este día. Recorrido local '
                        : ($localDay === 0 && $dayNumber > 1 ? 'Traslado hacia '.$municipality.' y recorrido local ' : 'Recorrido local '))
                        .$activityDescription.'. Este día no mezcla municipios alejados.',
                    'place_ids' => $placeIds,
                ]);
                $dayNumber++;
            }
            $previousAnchor = $anchor ?: $previousAnchor;
        }
        $company = match ($preferences['companion']) {
            'solo' => 'un viaje individual',
            'familia' => 'una experiencia familiar',
            'amigos' => 'un recorrido con amigos',
            default => 'un viaje en pareja',
        };
        $budget = match ($preferences['budget']) {
            'economico' => 'priorizando un recorrido práctico',
            'sin-limite' => 'con una selección flexible',
            default => 'manteniendo un ritmo equilibrado',
        };

        $feasibilityNote = $omittedMunicipalities->isNotEmpty()
            ? ' Por la duración elegida no es responsable incluir '.implode(', ', $omittedMunicipalities->all()).'; agrega más días para visitarlos.'
            : ' Cada día se concentra en un solo municipio para evitar recorridos inviables.';

        return [
            'introduction' => "Preparamos {$company} por {$destinations}, {$budget} y dando prioridad a tus intereses.{$feasibilityNote}",
            'days' => $dayPlans->all(),
        ];
    }

    private function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function buildRouteStarts($municipalities)
    {
        $cercadoStart = [
            'title' => 'Plaza Luis de Fuentes y Vargas',
            'address' => 'Centro de Tarija',
            'lat' => -21.5353,
            'lng' => -64.7294,
            'kind' => 'plaza',
        ];
        $plazas = AttractionPlace::query()
            ->where('activo', true)
            ->where(function ($query) {
                $query->where('titulo', 'like', '%plaza%')
                    ->orWhere('descripcion', 'like', '%plaza principal%');
            })
            ->get();

        return $municipalities->mapWithKeys(function (ProvinciaTuristica $municipality) use ($plazas, $cercadoStart) {
            $plaza = $plazas->first(fn (AttractionPlace $place) => str_contains(
                mb_strtolower(($place->titulo ?? '').' '.($place->direccion ?? '')),
                mb_strtolower($municipality->nombre)
            ));

            return [$municipality->nombre => $plaza ? [
                'title' => $plaza->titulo,
                'address' => $plaza->direccion,
                'lat' => (float) $plaza->latitud,
                'lng' => (float) $plaza->longitud,
                'kind' => 'plaza',
            ] : $cercadoStart];
        });
    }

    private function buildTravelContext(array $data, $municipalities, $places): array
    {
        $monthNumbers = collect($data['months'] ?? [])->map(fn (string $month) => [
            'Enero' => 1, 'Febrero' => 2, 'Marzo' => 3, 'Abril' => 4,
            'Mayo' => 5, 'Junio' => 6, 'Julio' => 7, 'Agosto' => 8,
            'Septiembre' => 9, 'Octubre' => 10, 'Noviembre' => 11, 'Diciembre' => 12,
        ][$month])->values();

        if (! empty($data['startDate'])) {
            $monthNumbers = collect([(int) Carbon::parse($data['startDate'])->month]);
        }

        $events = Evento::query()
            ->with('municipio')
            ->where('activo', true)
            ->whereNotNull('fecha_inicio')
            ->orderBy('fecha_inicio')
            ->get()
            ->filter(function (Evento $event) use ($municipalities, $monthNumbers, $data) {
                $isInMunicipality = $event->municipio_id
                    ? $municipalities->contains('id', $event->municipio_id)
                    : $municipalities->contains(
                        fn (ProvinciaTuristica $item) => str_contains(
                            mb_strtolower($event->lugar.' '.$event->descripcion),
                            mb_strtolower($item->nombre)
                        )
                    );
                if (! $isInMunicipality) {
                    return false;
                }
                if (! empty($data['startDate'])) {
                    $start = Carbon::parse($data['startDate'])->startOfDay();
                    $end = $start->copy()->addDays($data['duration'] - 1)->endOfDay();

                    return $event->fecha_inicio->lte($end)
                        && ($event->fecha_fin ?? $event->fecha_inicio)->gte($start);
                }

                return $monthNumbers->contains((int) $event->fecha_inicio->month);
            })
            ->take(12)
            ->map(fn (Evento $event) => [
                'title' => $event->titulo,
                'date' => $event->fecha_inicio->translatedFormat('d M Y'),
                'place' => $event->lugar,
            ])->values();

        $festivals = $municipalities->flatMap(fn (ProvinciaTuristica $municipality) => collect($municipality->fiestasLista())
            ->map(fn (string $festival) => $municipality->nombre.': '.$festival))
            ->take(15)
            ->values();

        $hotelPlaces = AttractionPlace::query()
            ->where('activo', true)
            ->with('type.parent')
            ->get()
            ->filter(fn (AttractionPlace $place) => $place->type?->parent?->slug === 'alojamiento' || $place->type?->slug === 'alojamiento')
            ->filter(fn (AttractionPlace $place) => $municipalities->contains(
                fn (ProvinciaTuristica $municipality) => str_contains(
                    mb_strtolower($place->direccion ?? ''),
                    mb_strtolower($municipality->nombre)
                )
            ))
            ->map(fn (AttractionPlace $hotel) => [
                'name' => $hotel->titulo,
                'price' => $hotel->precio,
                'roomOptions' => $hotel->room_options ?? [],
                'matchesRoom' => in_array($data['roomPreference'], $hotel->room_options ?? [], true),
            ])
            ->sortByDesc('matchesRoom')
            ->values();

        $weather = $this->getClimateContext($municipalities, $places, $monthNumbers, $data['startDate'] ?? null);
        $demandPressure = $events->isNotEmpty();

        return [
            'weather' => $weather,
            'weatherNote' => $weather->isEmpty()
                ? 'No fue posible consultar la fuente meteorológica en este momento.'
                : 'Fuera de los próximos 16 días se muestran promedios históricos orientativos; el pronóstico debe revisarse nuevamente cerca del viaje.',
            'events' => $events,
            'festivals' => $festivals,
            'hotels' => $hotelPlaces,
            'hotelAdvisory' => $demandPressure
                ? "Se priorizan hoteles con {$data['roomPreference']}. Hay eventos en el periodo: puede aumentar la demanda y las tarifas; confirma disponibilidad y precio."
                : "Se priorizan hoteles con {$data['roomPreference']}. Cuando el tipo de cama no esté publicado, confirma disponibilidad y precio directamente con el alojamiento.",
        ];
    }

    private function getClimateContext($municipalities, $places, $monthNumbers, ?string $startDate)
    {
        $points = $municipalities->map(function (ProvinciaTuristica $municipality) use ($places) {
            $place = $places->first(fn (AttractionPlace $item) => str_contains(
                mb_strtolower($item->direccion ?? ''),
                mb_strtolower($municipality->nombre)
            ));

            return $place ? [
                'municipality' => $municipality->nombre,
                'lat' => (float) $place->latitud,
                'lng' => (float) $place->longitud,
            ] : null;
        })->filter()->values();

        if ($points->isEmpty()) {
            return collect();
        }

        $exactForecast = $startDate
            && Carbon::parse($startDate)->isBetween(now()->startOfDay(), now()->copy()->addDays(15)->endOfDay());
        $endpoint = $exactForecast
            ? 'https://api.open-meteo.com/v1/forecast'
            : 'https://archive-api.open-meteo.com/v1/archive';

        try {
            $responses = Http::pool(fn (Pool $pool) => $points->map(function (array $point) use ($pool, $endpoint, $exactForecast, $startDate) {
                $params = [
                    'latitude' => $point['lat'],
                    'longitude' => $point['lng'],
                    'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum',
                    'timezone' => 'America/La_Paz',
                ];
                if ($exactForecast) {
                    $params['start_date'] = $startDate;
                    $params['end_date'] = Carbon::parse($startDate)
                        ->addDays(6)
                        ->min(now()->copy()->addDays(15))
                        ->format('Y-m-d');
                } else {
                    $params['start_date'] = '2021-01-01';
                    $params['end_date'] = '2025-12-31';
                }

                return $pool->timeout(12)->get($endpoint, $params);
            })->all());
        } catch (\Throwable) {
            return collect();
        }

        return $points->map(function (array $point, int $index) use ($responses, $monthNumbers, $exactForecast) {
            $response = $responses[$index] ?? null;
            if (! $response || ! $response->successful()) {
                return null;
            }
            $daily = $response->json('daily', []);
            $rows = collect($daily['time'] ?? [])->map(fn ($date, $i) => [
                'month' => (int) substr($date, 5, 2),
                'max' => $daily['temperature_2m_max'][$i] ?? null,
                'min' => $daily['temperature_2m_min'][$i] ?? null,
                'rain' => $daily['precipitation_sum'][$i] ?? null,
            ])->when(! $exactForecast, fn ($items) => $items->whereIn('month', $monthNumbers));
            if ($rows->isEmpty()) {
                return null;
            }

            return [
                'municipality' => $point['municipality'],
                'summary' => sprintf(
                    '%s: %.0f–%.0f °C; lluvia promedio %.1f mm/día.',
                    $exactForecast ? 'Pronóstico' : 'Promedio 2021–2025',
                    $rows->avg('min'),
                    $rows->avg('max'),
                    $rows->avg('rain')
                ),
            ];
        })->filter()->values();
    }
}
