<?php
namespace App\Http\Controllers;
use App\Models\AttractionPlace;
use App\Models\AttractionType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\TourismServiceProvider;
class InteractiveMapController extends Controller {
    public function __invoke(Request $request): View {
        $types = AttractionType::query()->where('activo', true)->with('parent')->orderBy('orden')->get();
        $focusedProvider = null;
        if ($request->filled('prestador')) {
            $focusedProvider = TourismServiceProvider::query()
                ->where('status', 'approved')
                ->with('mapPlace')
                ->findOrFail($request->integer('prestador'));
            abort_unless($focusedProvider->mapPlace?->activo, 404);
        }
        $places = AttractionPlace::query()
            ->where('activo', true)
            ->when(
                $focusedProvider,
                fn ($query) => $query->where('tourism_service_provider_id', $focusedProvider->id),
                fn ($query) => $query->whereNull('tourism_service_provider_id'),
            )
            ->with('type.parent')
            ->orderByDesc('destacado')->orderBy('orden')->get();

        return view('mapa-interactivo.index', [
            'attractionTypes' => $types,
            'attractionPlaces' => $places,
            'focusedProvider' => $focusedProvider,
            'mapPlacesData' => $places->map(fn (AttractionPlace $place) => [
                'id' => $place->id, 'title' => $place->titulo, 'summary' => $place->resumen, 'description' => $place->descripcion,
                'lat' => $place->latitud, 'lng' => $place->longitud, 'image' => $place->imagen_url, 'address' => $place->direccion,
                'phone' => $place->telefono, 'website' => $place->sitio_web, 'hours' => $place->horario, 'price' => $place->precio,
                'url' => route('lugares.show', $place),
                'typeId' => $place->attraction_type_id, 'parentId' => $place->type?->parent_id, 'type' => $place->type?->nombre,
                'color' => $place->type?->color ?: '#991b1b', 'icon' => $place->type?->icono ?: 'fa-location-dot',
            ])->values(),
            'mapTypesData' => $types->map(fn (AttractionType $type) => [
                'id' => $type->id, 'parentId' => $type->parent_id, 'name' => $type->nombre,
                'description' => $type->descripcion, 'activities' => $type->que_hacer,
                'color' => $type->color, 'icon' => $type->icono,
            ])->values(),
            'googleMapsKey' => config('services.google_maps.key'),
        ]);
    }
}
