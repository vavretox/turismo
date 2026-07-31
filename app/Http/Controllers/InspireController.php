<?php

namespace App\Http\Controllers;

use App\Models\AttractionPlace;
use App\Models\AttractionType;
use App\Models\Destino;
use App\Models\ProvinciaTuristica;
use Illuminate\Contracts\View\View;

class InspireController extends Controller
{
    public function __invoke(): View
    {
        $places = AttractionPlace::query()
            ->where('activo', true)
            ->with('type.parent')
            ->orderByDesc('destacado')
            ->orderBy('orden')
            ->get();

        $municipalities = ProvinciaTuristica::query()
            ->where('activo', true)
            ->orderBy('orden')
            ->get(['nombre', 'provincia']);

        $destinationPlaces = Destino::query()
            ->where('activo', true)
            ->whereNotNull('municipio_id')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->with(['categoria', 'municipio'])
            ->orderByDesc('destacado')
            ->orderBy('orden')
            ->get()
            ->map(fn (Destino $destination): array => [
                'id' => 'destino-'.$destination->id,
                'title' => $destination->nombre,
                'summary' => $destination->resumen,
                'image' => $destination->imagen_url,
                'address' => $destination->ubicacion,
                'lat' => (float) $destination->latitud,
                'lng' => (float) $destination->longitud,
                'featured' => $destination->destacado,
                'typeId' => null,
                'type' => $destination->categoria?->nombre ?: 'Destino turístico',
                'subtype' => $destination->categoria?->nombre ?: 'Destino turístico',
                'municipality' => $destination->municipio?->nombre,
                'roomOptions' => [],
                'isHotel' => false,
                'url' => route('destinos.show', $destination),
            ]);

        return view('inspirame.index', [
            'municipalities' => $municipalities,
            'interests' => AttractionType::query()
                ->where('activo', true)
                ->whereNull('parent_id')
                ->where('slug', '!=', 'alojamiento')
                ->orderBy('orden')
                ->get(),
            'plannerPlaces' => $places->map(function (AttractionPlace $place) use ($municipalities) {
                $address = mb_strtolower($place->direccion ?? '');
                $municipality = $municipalities->first(
                    fn (ProvinciaTuristica $item) => str_contains($address, mb_strtolower($item->nombre))
                );

                if (! $municipality) {
                    $municipality = $municipalities
                        ->groupBy('provincia')
                        ->first(fn ($items, $province) => $items->count() === 1 && str_contains($address, mb_strtolower($province)))
                        ?->first();
                }

                return [
                    'id' => $place->id,
                    'title' => $place->titulo,
                    'summary' => $place->resumen,
                    'image' => $place->imagen_url,
                    'address' => $place->direccion,
                    'lat' => $place->latitud,
                    'lng' => $place->longitud,
                    'featured' => $place->destacado,
                    'typeId' => $place->type?->parent_id ?: $place->attraction_type_id,
                    'type' => $place->type?->parent?->nombre ?: $place->type?->nombre,
                    'subtype' => $place->type?->nombre,
                    'municipality' => $municipality?->nombre,
                    'roomOptions' => $place->room_options ?? [],
                    'isHotel' => ($place->type?->parent?->slug === 'alojamiento' || $place->type?->slug === 'alojamiento'),
                ];
            })->concat($destinationPlaces)->values(),
        ]);
    }
}
