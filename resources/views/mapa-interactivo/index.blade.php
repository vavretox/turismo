@extends('layouts.app')
@section('title', 'Mapa interactivo de Tarija')
@section('description', 'Explora atractivos, gastronomía, cultura, naturaleza y lugares de interés de Tarija.')

@if(request()->boolean('embed'))
    @push('scripts')
        <style>
            .site-navbar, body > footer, #tourism-widget, #weekly-activity-popup,
            .global-social-links, .provider-back-button, #page-translator { display: none !important; }
            .map-explorer { padding-top: 0 !important; }
            .map-explorer-heading { display: none !important; }
            .map-category-bar { top: 0 !important; }
            .map-explorer > .grid { min-height: 620px !important; }
            .map-explorer > .grid > aside { max-height: 620px !important; }
            .map-explorer > .grid > div { min-height: 620px !important; }
            .map-place-list { height: 490px !important; }
            body { overflow: hidden; }
            @media (max-width: 767px) {
                .map-explorer > .grid { min-height: 0 !important; }
                .map-explorer > .grid > aside { display: none !important; }
                .map-explorer > .grid > div { min-height: 72dvh !important; }
                .map-place-list { height: auto !important; }
            }
        </style>
    @endpush
@endif

@section('content')
<section class="map-explorer" id="map-explorer">
    <div class="map-explorer-heading">
        <div class="container-custom flex flex-col justify-between gap-5 py-8 lg:flex-row lg:items-end">
            <div>
                <p class="text-sm font-black uppercase tracking-[.2em] text-amber-500">{{ $focusedProvider ? 'Ubicación del prestador' : 'Cercado · Tarija, Bolivia' }}</p>
                <h1 class="mt-2 text-4xl font-black text-white md:text-5xl">{{ $focusedProvider?->commercial_name ?: 'Mapa de lugares de interés' }}</h1>
                <p class="mt-3 max-w-2xl text-white/70">{{ $focusedProvider ? 'Consulta su ubicación exacta, información principal y cómo llegar.' : 'Filtra por tipo de atractivo, descubre qué puedes hacer y selecciona un marcador para consultar sus detalles.' }}</p>
            </div>
            <div class="rounded-2xl bg-white/10 px-5 py-3 text-sm font-bold text-white backdrop-blur"><i class="fa-solid fa-location-crosshairs mr-2 text-amber-400"></i><span id="map-result-count">{{ $attractionPlaces->count() }}</span> lugares publicados</div>
        </div>
    </div>

    @unless($focusedProvider)
    <div class="map-category-bar">
        <div class="container-custom flex gap-2 overflow-x-auto py-4">
            <button class="map-filter is-active" data-type-filter="all"><i class="fa-solid fa-border-all"></i>Todos</button>
            @foreach($attractionTypes->whereNull('parent_id')->reject(fn ($type) => Str::contains(Str::lower($type->nombre), ['alojamiento', 'hotel'])) as $type)
                <button class="map-filter" data-type-filter="{{ $type->id }}" style="--filter-color:{{ $type->color }}"><i class="fa-solid {{ $type->icono }}"></i>{{ $type->nombre }}</button>
            @endforeach
        </div>
    </div>
    @endunless

    <div class="grid min-h-[620px] lg:grid-cols-[330px_1fr]">
        <aside class="map-sidebar">
            <div class="mobile-map-list-heading">
                <span>Atractivos turísticos</span>
                <strong>Descubre lugares</strong>
            </div>
            <div class="p-4">
                <label class="map-search"><i class="fa-solid fa-search"></i><input id="map-place-search" placeholder="Buscar atractivo o lugar..."></label>
            </div>
            <div id="map-subcategories" class="flex flex-wrap gap-2 px-4 pb-3"></div>
            <div id="map-place-list" class="map-place-list">
                @forelse($attractionPlaces as $place)
                    <button class="map-place-card" data-place-id="{{ $place->id }}" data-type-id="{{ $place->attraction_type_id }}" data-parent-id="{{ $place->type?->parent_id }}" data-provider-type="{{ $place->serviceProvider?->provider_type }}" data-search="{{ Str::lower($place->titulo.' '.$place->resumen.' '.$place->direccion.' '.$place->type?->nombre) }}">
                        <img src="{{ $place->imagen_url }}" alt="{{ $place->titulo }}">
                        <span class="min-w-0"><small style="color:{{ $place->type?->color }}">{{ $place->type?->nombre }}</small><strong>{{ $place->titulo }}</strong><em><i class="fa-solid fa-location-dot"></i>{{ $place->direccion ?: 'Tarija' }}</em></span>
                    </button>
                @empty
                    <div class="m-4 rounded-2xl bg-amber-50 p-5 text-sm leading-6 text-amber-900"><strong>Aún no hay lugares publicados.</strong><br>Registra tipos de atractivo y lugares con coordenadas desde el panel administrativo.</div>
                @endforelse
            </div>
        </aside>
        <div class="relative min-h-[540px]">
            <div class="absolute inset-0 bg-[#e5e7eb]">
                <iframe
                    class="absolute inset-0 h-full w-full border-0"
                    src="https://www.openstreetmap.org/export/embed.html?bbox=-65.30%2C-21.95%2C-64.45%2C-21.25&amp;layer=mapnik&amp;marker=-21.5355%2C-64.7296"
                    title="Mapa libre de Tarija"
                    loading="eager"
                ></iframe>
                <div id="fallback-map-markers" class="fallback-map-markers">
                    @foreach($attractionPlaces as $place)
                        @php
                            $markerLeft = max(2, min(98, (($place->longitud - (-65.30)) / ((-64.45) - (-65.30))) * 100));
                            $markerTop = max(3, min(97, (((-21.25) - $place->latitud) / ((-21.25) - (-21.95))) * 100));
                        @endphp
                        <button
                            type="button"
                            class="fallback-map-marker"
                            data-fallback-place="{{ $place->id }}"
                            style="left:{{ $markerLeft }}%;top:{{ $markerTop }}%;--marker-color:{{ $place->type?->color ?: '#991b1b' }}"
                            aria-label="Ver {{ $place->titulo }}"
                        >
                            <i class="fa-solid {{ $place->type?->icono ?: 'fa-location-dot' }}"></i>
                            <span>{{ $place->titulo }}</span>
                        </button>
                    @endforeach
                </div>
                <div id="google-attractions-map" class="leaflet-map-canvas absolute inset-0"></div>
            </div>
            <div id="map-place-detail" class="map-place-detail" hidden></div>
            <div class="map-layer-switcher" aria-label="Cambiar capa del mapa">
                <button class="is-active" type="button" data-map-layer="map"><i class="fa-solid fa-map"></i><span>Mapa</span></button>
                <button type="button" data-map-layer="roads"><i class="fa-solid fa-road"></i><span>Carreteras</span></button>
                <button type="button" data-map-layer="satellite"><i class="fa-solid fa-satellite"></i><span>Satélite</span></button>
                <button type="button" data-map-layer="terrain"><i class="fa-solid fa-mountain-sun"></i><span>Relieve</span></button>
                <button type="button" data-map-layer="earth"><i class="fa-solid fa-cubes"></i><span>Earth 3D</span></button>
            </div>
            <div class="map-api-notice"><i class="fa-solid fa-map"></i>Mapa libre con OpenStreetMap · sin claves ni costos de API</div>
        </div>
    </div>
</section>

<div id="directions-modal" class="directions-modal" hidden>
    <div class="directions-dialog" role="dialog" aria-modal="true" aria-labelledby="directions-title">
        <button id="directions-close" class="directions-close" type="button"><i class="fa-solid fa-xmark"></i></button>
        <div class="directions-icon"><i class="fa-solid fa-diamond-turn-right"></i></div>
        <p class="text-xs font-black uppercase tracking-[.18em] text-red-700">Planifica tu recorrido</p>
        <h2 id="directions-title" class="mt-2 text-2xl font-black">Cómo llegar</h2>
        <p id="directions-destination" class="mt-2 text-sm text-gray-600"></p>
        <div class="mt-6 grid gap-3 sm:grid-cols-3">
            <a id="directions-walking" class="direction-option" target="_blank" rel="noopener"><i class="fa-solid fa-person-walking"></i><strong>Caminando</strong><span>Ruta peatonal</span></a>
            <a id="directions-driving" class="direction-option" target="_blank" rel="noopener"><i class="fa-solid fa-car-side"></i><strong>En automóvil</strong><span>Ruta vehicular</span></a>
            <a id="directions-alternatives" class="direction-option" target="_blank" rel="noopener"><i class="fa-solid fa-route"></i><strong>Alternativas</strong><span>Comparar rutas</span></a>
        </div>
        <p class="mt-5 rounded-xl bg-amber-50 p-3 text-xs leading-5 text-amber-900"><i class="fa-solid fa-circle-info mr-1"></i>Google Maps utilizará tu ubicación actual como punto de partida cuando cuente con permiso.</p>
    </div>
</div>

<script>
window.tarijaAttractionData = {{ Illuminate\Support\Js::from($mapPlacesData) }};
window.tarijaAttractionTypes = {{ Illuminate\Support\Js::from($mapTypesData) }};
</script>
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const places = window.tarijaAttractionData || [];
    const detail = document.getElementById('map-place-detail');
    const subcategories = document.getElementById('map-subcategories');
    const modal = document.getElementById('directions-modal');
    const safe = function (value) { const node = document.createElement('span'); node.textContent = value || ''; return node.innerHTML; };

    document.querySelectorAll('[data-fallback-place]').forEach(function (marker) {
        marker.addEventListener('click', function () {
            const place = places.find(function (item) { return item.id === Number(marker.dataset.fallbackPlace); });
            if (!place) return;
            detail.hidden = false;
            detail.innerHTML = '<button class="map-detail-close"><i class="fa-solid fa-xmark"></i></button>' +
                '<img src="' + safe(place.image) + '" alt="' + safe(place.title) + '">' +
                '<div class="p-5"><small style="color:' + safe(place.color) + '">' + safe(place.type) + '</small>' +
                '<h2>' + safe(place.title) + '</h2><p>' + safe(place.summary || place.description) + '</p>' +
                '<div class="mt-3 grid gap-2 text-xs"><span><i class="fa-solid fa-location-dot"></i>' + safe(place.address || 'Tarija') + '</span></div>' +
                '<div class="map-detail-actions"><a class="map-directions-button" href="' + safe(place.url) + '"><i class="fa-solid fa-circle-info"></i> Ver página</a><button class="map-directions-button"><i class="fa-solid fa-diamond-turn-right"></i> Cómo llegar</button></div></div>';
            detail.querySelector('.map-detail-close').addEventListener('click', function () { detail.hidden = true; });
            detail.querySelector('.map-directions-button').addEventListener('click', function () {
                const destination = place.lat + ',' + place.lng;
                const base = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(destination);
                document.getElementById('directions-destination').textContent = place.title + (place.address ? ' · ' + place.address : '');
                document.getElementById('directions-walking').href = base + '&travelmode=walking';
                document.getElementById('directions-driving').href = base + '&travelmode=driving';
                document.getElementById('directions-alternatives').href = base;
                modal.hidden = false;
                requestAnimationFrame(function () { modal.classList.add('is-visible'); });
            });
        });
    });
    const closeDirections = function () {
        modal.classList.remove('is-visible');
        setTimeout(function () { modal.hidden = true; }, 200);
    };
    document.getElementById('directions-close')?.addEventListener('click', closeDirections);
    modal?.addEventListener('click', function (event) { if (event.target === modal) closeDirections(); });

    const canvas = document.getElementById('google-attractions-map');
    if (window.L && canvas && !canvas._leaflet_id) {
        canvas.dataset.freeMapLoaded = 'true';
        canvas.classList.add('is-ready');
        canvas.previousElementSibling.hidden = true;
        document.getElementById('fallback-map-markers').hidden = true;

        const map = window.L.map(canvas, {
            scrollWheelZoom: window.matchMedia('(min-width: 768px) and (pointer: fine)').matches,
            zoomControl: false,
            tap: true,
            touchZoom: true
        }).setView([-21.5355, -64.7296], 11);
        window.L.control.zoom({ position: 'bottomright' }).addTo(map);
        const baseLayers = {
            map: window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
            }),
            roads: window.L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 20, attribution: '&copy; OpenStreetMap &copy; CARTO'
            }),
            satellite: window.L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19, attribution: 'Tiles &copy; Esri'
            }),
            terrain: window.L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                maxZoom: 17, attribution: 'Map data &copy; OpenStreetMap, SRTM | Map style &copy; OpenTopoMap'
            })
        };
        let activeBaseLayer = baseLayers.map.addTo(map);

        document.querySelectorAll('[data-map-layer]').forEach(function (button) {
            button.addEventListener('click', function () {
                const layerName = button.dataset.mapLayer;
                if (layerName === 'earth') {
                    const center = map.getCenter();
                    window.open('https://earth.google.com/web/@' + center.lat + ',' + center.lng + ',2500a,12000d,35y,0h,55t,0r', '_blank', 'noopener');
                    return;
                }
                if (!baseLayers[layerName]) return;
                map.removeLayer(activeBaseLayer);
                activeBaseLayer = baseLayers[layerName].addTo(map);
                document.querySelectorAll('[data-map-layer]').forEach(function (item) {
                    item.classList.toggle('is-active', item === button);
                });
            });
        });

        const nativeMarkers = new Map();
        places.forEach(function (place) {
            const marker = window.L.circleMarker([place.lat, place.lng], {
                radius: 11,
                fillColor: place.color || '#991b1b',
                fillOpacity: 1,
                color: '#ffffff',
                weight: 3
            }).bindTooltip(place.title, { direction: 'top', offset: [0, -10] }).addTo(map);
            marker.on('click', function () {
                document.querySelector('[data-fallback-place="' + place.id + '"]')?.click();
            });
            nativeMarkers.set(place.id, marker);
        });

        document.querySelectorAll('.map-place-card').forEach(function (card) {
            card.addEventListener('click', function () {
                const place = places.find(function (item) { return item.id === Number(card.dataset.placeId); });
                if (!place) return;
                map.flyTo([place.lat, place.lng], 15, { duration: 1 });
                nativeMarkers.get(place.id)?.openTooltip();
                document.querySelector('[data-fallback-place="' + place.id + '"]')?.click();
            });
        });

        let selectedType = 'all';
        let searchTerm = '';
        const applyMapFilters = function () {
            let total = 0;
            places.forEach(function (place) {
                const matchesType = selectedType === 'all' || String(place.typeId) === selectedType || String(place.parentId) === selectedType;
                const text = (place.title + ' ' + (place.summary || '') + ' ' + (place.address || '') + ' ' + (place.type || '')).toLowerCase();
                const show = matchesType && text.includes(searchTerm);
                const marker = nativeMarkers.get(place.id);
                if (show && !map.hasLayer(marker)) marker.addTo(map);
                if (!show && map.hasLayer(marker)) marker.remove();
                const card = document.querySelector('.map-place-card[data-place-id="' + place.id + '"]');
                if (card) card.hidden = !show;
                if (show) total++;
            });
            document.getElementById('map-result-count').textContent = total;
        };
        document.querySelectorAll('[data-type-filter]').forEach(function (button) {
            button.addEventListener('click', function () {
                selectedType = button.dataset.typeFilter;
                document.querySelectorAll('[data-type-filter]').forEach(function (item) { item.classList.toggle('is-active', item === button); });
                applyMapFilters();
            });
        });
        document.getElementById('map-place-search')?.addEventListener('input', function (event) {
            searchTerm = event.target.value.trim().toLowerCase();
            applyMapFilters();
        });
        setTimeout(function () { map.invalidateSize(); }, 250);
    }
});
</script>
@endsection
