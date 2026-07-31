@extends('layouts.app')

@section('title', $destino->nombre)
@section('description', $destino->resumen)

@section('content')
@php
    $galeriaDestino = collect($destino->imagenesSecundariasUrls())->filter()->values();
    $recomendaciones = collect(preg_split('/\r\n|\r|\n/', (string) $destino->recomendaciones))->map(fn ($item) => trim($item))->filter();
    $rutasLlegada = collect($destino->rutas_llegada ?? [])->filter(fn ($ruta) => filled($ruta['nombre'] ?? null))->values();
@endphp

<section class="relative min-h-[92svh] overflow-hidden bg-gray-950 text-white">
    <img class="absolute inset-0 h-full w-full object-cover" src="{{ $destino->imagen_url }}" alt="{{ $destino->nombre }}">
    <div class="absolute inset-0 bg-gradient-to-r from-gray-950/85 via-gray-950/40 to-transparent"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-gray-950/90 via-transparent to-gray-950/20"></div>
    <div class="container-custom relative flex min-h-[92svh] flex-col justify-end pb-16 pt-32 md:pb-20">
        <nav class="mb-auto flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-wider text-white/70" aria-label="Migas de pan">
            <a class="transition hover:text-white" href="{{ route('home') }}">{{ __('ui.home') }}</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a class="transition hover:text-white" href="{{ route('destinos') }}">{{ __('ui.destinations') }}</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-white">{{ $destino->nombre }}</span>
        </nav>
        <div class="max-w-4xl">
            <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[.2em] backdrop-blur">
                <i class="fa-solid fa-location-dot text-[#eadfd2]"></i>{{ $destino->municipio?->nombre ?: ($destino->ubicacion ?: 'Tarija') }}
            </p>
            <p class="text-sm font-bold uppercase tracking-[.2em] text-[#eadfd2]">{{ $destino->categoria?->nombre ?? 'Destino de Tarija' }}</p>
            <h1 class="mt-3 max-w-4xl font-display text-5xl font-black leading-[.95] md:text-7xl lg:text-8xl">{{ $destino->nombre }}</h1>
            <p class="mt-6 max-w-2xl text-xl leading-8 text-white/85 md:text-2xl">{{ $destino->subtitulo ?: $destino->resumen }}</p>
            <a href="#descubrir" class="mt-9 inline-flex items-center gap-3 rounded-full bg-white px-6 py-3 text-sm font-black text-red-950 shadow-xl transition hover:-translate-y-1">
                {{ __('catalog.discover_destination') }} <i class="fa-solid fa-arrow-down"></i>
            </a>
        </div>
    </div>
</section>

<nav class="sticky top-20 z-30 border-b border-red-100 bg-white/95 shadow-sm backdrop-blur" aria-label="Contenido de la página">
    <div class="container-custom flex gap-2 overflow-x-auto py-3 text-sm font-bold">
        <a class="shrink-0 rounded-full bg-red-950 px-4 py-2 text-white" href="#descubrir">{{ __('catalog.destination') }}</a>
        @if($galeriaDestino->isNotEmpty())<a class="shrink-0 rounded-full px-4 py-2 text-gray-600 transition hover:bg-red-50 hover:text-red-900" href="#fotografias">Fotografías</a>@endif
        <a class="shrink-0 rounded-full px-4 py-2 text-gray-600 transition hover:bg-red-50 hover:text-red-900" href="#como-llegar">{{ __('catalog.directions') }}</a>
        @if($destinosRelacionados->isNotEmpty())<a class="shrink-0 rounded-full px-4 py-2 text-gray-600 transition hover:bg-red-50 hover:text-red-900" href="#otros-destinos">Otros destinos</a>@endif
    </div>
</nav>

<section id="descubrir" class="scroll-mt-36 bg-white py-16 md:py-24">
    <div class="container-custom grid gap-12 lg:grid-cols-[minmax(0,1fr)_340px] lg:gap-20">
        <article>
            <p class="text-sm font-black uppercase tracking-[.2em] text-red-700">{{ __('catalog.memorable') }}</p>
            <h2 class="mt-4 max-w-3xl text-4xl font-black leading-tight text-gray-950 md:text-5xl">
                {{ $destino->introduccion ?: $destino->resumen }}
            </h2>
            <div class="mt-9 max-w-3xl whitespace-pre-line text-lg leading-8 text-gray-600">{{ $destino->descripcion ?: $destino->resumen }}</div>

            @if($recomendaciones->isNotEmpty())
                <div class="mt-12 rounded-3xl bg-[#f8f3ec] p-7 ring-1 ring-red-100 md:p-9">
                    <h3 class="flex items-center gap-3 text-2xl font-black text-gray-950"><i class="fa-regular fa-compass text-red-800"></i>{{ __('catalog.before_travel') }}</h3>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach($recomendaciones as $recomendacion)
                            <p class="flex gap-3 rounded-2xl bg-white p-4 text-sm leading-6 text-gray-700 shadow-sm">
                                <i class="fa-solid fa-check mt-1 text-green-700"></i><span>{{ $recomendacion }}</span>
                            </p>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>

        <aside>
            <div class="sticky top-40 overflow-hidden rounded-3xl bg-red-950 text-white shadow-2xl shadow-red-950/20">
                <div class="p-7">
                    <p class="text-xs font-black uppercase tracking-[.2em] text-[#eadfd2]">Información práctica</p>
                    <h2 class="mt-2 text-2xl font-black">{{ __('catalog.plan_visit') }}</h2>
                    <div class="mt-7 space-y-6 text-sm text-white/75">
                        <p class="flex gap-4"><i class="fa-solid fa-location-dot mt-1 w-5 text-[#eadfd2]"></i><span><strong class="block text-white">{{ __('catalog.municipality') }}</strong>{{ $destino->municipio?->nombre ?: 'Por definir' }}@if($destino->ubicacion)<small class="mt-1 block text-white/70">{{ $destino->ubicacion }}</small>@endif</span></p>
                        @if($destino->mejor_epoca)<p class="flex gap-4"><i class="fa-regular fa-calendar mt-1 w-5 text-[#eadfd2]"></i><span><strong class="block text-white">Mejor época</strong>{{ $destino->mejor_epoca }}</span></p>@endif
                        @if($destino->duracion_recomendada)<p class="flex gap-4"><i class="fa-regular fa-clock mt-1 w-5 text-[#eadfd2]"></i><span><strong class="block text-white">Duración recomendada</strong>{{ $destino->duracion_recomendada }}</span></p>@endif
                        @if($destino->precio)<p class="flex gap-4"><i class="fa-solid fa-tag mt-1 w-5 text-[#eadfd2]"></i><span><strong class="block text-white">Precio referencial</strong>Desde ${{ number_format($destino->precio, 2) }}</span></p>@endif
                        <p class="flex gap-4"><i class="fa-solid fa-layer-group mt-1 w-5 text-[#eadfd2]"></i><span><strong class="block text-white">Tipo de experiencia</strong>{{ $destino->categoria?->nombre ?? 'Turismo y cultura' }}</span></p>
                    </div>
                    <a class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3.5 font-black text-red-950 transition hover:bg-[#eadfd2]" href="{{ route('contacto') }}">Solicitar información <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
        </aside>
    </div>
</section>

@if($galeriaDestino->isNotEmpty())
<section id="fotografias" class="scroll-mt-32 bg-[#f8f3ec] py-16 md:py-24" x-data="{ images: @js($galeriaDestino), active: 0, open: false, show(i) { this.active=i; this.open=true; document.body.style.overflow='hidden' }, close() { this.open=false; document.body.style.overflow='' }, previous() { this.active=(this.active-1+this.images.length)%this.images.length }, next() { this.active=(this.active+1)%this.images.length } }" @keydown.escape.window="close()" @keydown.left.window="if(open) previous()" @keydown.right.window="if(open) next()">
    <div class="container-custom">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
            <div><p class="text-sm font-black uppercase tracking-[.2em] text-red-700">Banco de fotografías</p><h2 class="section-title mt-2">Mira, siente y descubre</h2><p class="section-subtitle">Una mirada más cercana a {{ $destino->nombre }}.</p></div>
            <span class="w-fit rounded-full bg-white px-4 py-2 text-sm font-bold text-red-900 shadow-sm"><i class="fa-regular fa-images mr-2"></i>{{ $galeriaDestino->count() }} fotos</span>
        </div>
        <div class="mt-9 grid grid-cols-2 gap-3 md:grid-cols-4 md:grid-rows-2 md:gap-5">
            @foreach($galeriaDestino->take(7) as $index => $foto)
                <button type="button" class="group relative min-h-48 overflow-hidden rounded-2xl bg-gray-200 shadow-lg @if($index === 0) col-span-2 row-span-2 md:min-h-[520px] @else md:min-h-0 @endif" @click="show({{ $index }})">
                    <img class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110" src="{{ $foto }}" alt="Fotografía {{ $index + 1 }} de {{ $destino->nombre }}" loading="lazy">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-50 transition group-hover:opacity-80"></span>
                    <span class="absolute bottom-3 right-3 grid h-10 w-10 place-items-center rounded-full bg-white/90 text-red-900"><i class="fa-solid fa-expand"></i></span>
                </button>
            @endforeach
        </div>
    </div>
    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-950/95 p-4" role="dialog" aria-modal="true" @click.self="close()">
        <button class="absolute right-4 top-4 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white hover:text-red-900" @click="close()"><i class="fa-solid fa-xmark"></i></button>
        <button x-show="images.length>1" class="absolute left-3 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white hover:text-red-900 md:left-8" @click.stop="previous()"><i class="fa-solid fa-chevron-left"></i></button>
        <figure class="flex max-h-full max-w-6xl flex-col items-center"><img class="max-h-[84vh] max-w-full rounded-xl object-contain shadow-2xl" :src="images[active]" alt="Fotografía ampliada"><figcaption class="mt-4 rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-white"><span x-text="active+1"></span> / <span x-text="images.length"></span></figcaption></figure>
        <button x-show="images.length>1" class="absolute right-3 grid h-12 w-12 place-items-center rounded-full bg-white/10 text-white hover:bg-white hover:text-red-900 md:right-8" @click.stop="next()"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
</section>
@endif

<section id="como-llegar" class="scroll-mt-32 bg-white py-12 md:py-16">
    <div class="container-custom">
        <div class="mx-auto mb-8 max-w-3xl text-center">
            <p class="text-sm font-black uppercase tracking-[.2em] text-red-700">{{ __('catalog.prepare_route') }}</p>
            <h2 class="section-title mt-3">¿Cómo llegar a {{ $destino->nombre }}?</h2>
            @if($destino->como_llegar)<p class="section-subtitle mx-auto whitespace-pre-line">{{ $destino->como_llegar }}</p>@endif
        </div>

        <div class="overflow-hidden rounded-[32px] bg-white shadow-2xl shadow-red-950/15 ring-1 ring-red-100" x-data="{ selected: 0 }">
            <div class="grid lg:h-[620px] lg:grid-cols-[minmax(0,1.15fr)_minmax(420px,.85fr)]">
                <div class="relative min-h-[360px] overflow-hidden bg-[#e9eee8] lg:min-h-0">
                    @if($destino->latitud && $destino->longitud)
                        <div id="destination-route-map" class="absolute inset-0 h-full w-full" aria-label="Mapa de rutas hacia {{ $destino->nombre }}"></div>
                    @else
                        <div class="flex h-full min-h-[360px] items-center justify-center bg-gradient-to-br from-[#e9eee8] to-[#d8e4dc] px-6 text-center">
                            <div>
                                <span class="mx-auto grid h-16 w-16 place-items-center rounded-full bg-white text-2xl text-red-800 shadow-lg"><i class="fa-solid fa-map-location-dot"></i></span>
                                <p class="mt-5 font-black text-gray-950">Ubicación de {{ $destino->nombre }}</p>
                                <p class="mt-2 text-sm text-gray-600">Añade sus coordenadas desde el administrador para mostrar aquí el recorrido interactivo.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="min-w-0 border-t border-gray-100 lg:h-[620px] lg:overflow-y-auto lg:border-l lg:border-t-0">
            @if($rutasLlegada->isNotEmpty())
                <div class="sticky top-0 z-10 border-b border-gray-100 bg-white/95 px-3 pt-3 backdrop-blur md:px-5">
                    <div class="flex gap-1 overflow-x-auto">
                        @foreach($rutasLlegada as $routeIndex => $ruta)
                            <button
                                type="button"
                                class="flex min-w-32 shrink-0 items-center justify-center gap-2 border-b-2 px-3 py-3 text-xs font-black transition"
                                :class="selected === {{ $routeIndex }} ? 'border-red-700 text-red-800' : 'border-transparent text-gray-500 hover:text-red-800'"
                                @click="selected={{ $routeIndex }}; window.dispatchEvent(new CustomEvent('destination-route:change', { detail: { index: {{ $routeIndex }} } }))"
                            >
                                <i class="fa-solid fa-flag"></i>{{ $ruta['nombre'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="p-5 md:p-6">
                    @foreach($rutasLlegada as $routeIndex => $ruta)
                        <div x-cloak x-show="selected === {{ $routeIndex }}">
                            <div class="mb-5 flex flex-col justify-between gap-3 xl:flex-row xl:items-center">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-wider text-red-700">Salida desde {{ $ruta['origen'] ?? 'origen definido' }}</p>
                                    <h3 class="mt-1 text-xl font-black text-gray-950">{{ $ruta['nombre'] }}</h3>
                                    @if(filled($ruta['descripcion'] ?? null))<p class="mt-1 text-xs leading-5 text-gray-600">{{ $ruta['descripcion'] }}</p>@endif
                                </div>
                                <a class="inline-flex w-fit shrink-0 items-center gap-2 rounded-full bg-red-950 px-4 py-2.5 text-xs font-black text-white transition hover:bg-red-800" target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination={{ $destino->latitud && $destino->longitud ? $destino->latitud.','.$destino->longitud : urlencode($destino->nombre.', '.($destino->ubicacion ?: 'Tarija, Bolivia')) }}">
                                    Desde mi ubicación <i class="fa-solid fa-location-arrow"></i>
                                </a>
                            </div>
                            <div class="grid gap-3">
                                <div class="mb-1 flex items-center gap-3 text-xs font-black uppercase tracking-[.16em] text-gray-500">
                                    <i class="fa-solid fa-route text-red-800"></i>Puntos de control del recorrido
                                </div>
                                @foreach(($ruta['tramos'] ?? []) as $segmentIndex => $tramo)
                                    @php
                                        $transportIcons = ['avion' => 'fa-plane', 'bus' => 'fa-bus', 'auto' => 'fa-car', 'caminata' => 'fa-person-walking', 'bicicleta' => 'fa-bicycle', 'otro' => 'fa-route'];
                                        $transportLabels = ['avion' => 'Avión', 'bus' => 'Bus', 'auto' => 'Automóvil', 'caminata' => 'Caminata', 'bicicleta' => 'Bicicleta', 'otro' => 'Otro'];
                                        $medio = $tramo['medio'] ?? 'otro';
                                    @endphp
                                    <article class="relative grid gap-3 rounded-2xl bg-gray-50 p-4 ring-1 ring-gray-100 sm:grid-cols-[46px_1fr] xl:grid-cols-[46px_1fr_auto] xl:items-center">
                                        <span class="relative grid h-11 w-11 place-items-center rounded-xl bg-white text-base text-red-800 shadow-sm"><small class="absolute -left-2 -top-2 grid h-5 w-5 place-items-center rounded-full bg-red-800 text-[9px] font-black text-white ring-2 ring-white">{{ $segmentIndex + 1 }}</small><i class="fa-solid {{ $transportIcons[$medio] ?? 'fa-route' }}"></i></span>
                                        <div>
                                            <p class="text-sm font-black text-gray-950">{{ $tramo['desde'] ?? '' }} <i class="fa-solid fa-arrow-right mx-1 text-[9px] text-red-700"></i> {{ $tramo['hasta'] ?? '' }}</p>
                                            <p class="mt-1 text-xs leading-5 text-gray-500">{{ $transportLabels[$medio] ?? 'Traslado' }}@if(filled($tramo['indicaciones'] ?? null)) · {{ $tramo['indicaciones'] }}@endif</p>
                                        </div>
                                        @if(filled($tramo['duracion'] ?? null))<span class="w-fit rounded-full bg-white px-3 py-2 text-[10px] font-black text-red-900 shadow-sm sm:col-start-2 xl:col-start-auto"><i class="fa-regular fa-clock mr-1"></i>{{ $tramo['duracion'] }}</span>@endif
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center md:p-12">
                    <h3 class="text-2xl font-black text-gray-950">Llega desde donde te encuentres</h3>
                    <p class="mx-auto mt-3 max-w-xl text-gray-600">Google Maps utilizará tu ubicación actual y calculará la mejor ruta disponible hasta {{ $destino->nombre }}.</p>
                    <a class="mt-6 inline-flex items-center gap-2 rounded-full bg-red-950 px-5 py-3 text-sm font-black text-white transition hover:bg-red-800" target="_blank" rel="noopener" href="https://www.google.com/maps/dir/?api=1&destination={{ $destino->latitud && $destino->longitud ? $destino->latitud.','.$destino->longitud : urlencode($destino->nombre.', '.($destino->ubicacion ?: 'Tarija, Bolivia')) }}">
                        Cómo llegar desde mi ubicación <i class="fa-solid fa-location-arrow"></i>
                    </a>
                </div>
            @endif
                </div>
            </div>
        </div>
    </div>
</section>

@if($destinosRelacionados->isNotEmpty())
<section id="otros-destinos" class="scroll-mt-32 bg-[#f8f3ec] py-16 md:py-24">
    <div class="container-custom">
        <p class="text-sm font-black uppercase tracking-[.2em] text-red-700">Continúa explorando</p>
        <h2 class="section-title mt-2">Otros destinos para ti</h2>
        <div class="mt-9 grid gap-6 md:grid-cols-3">
            @foreach($destinosRelacionados as $relacionado)
                <a href="{{ route('destinos.show', $relacionado) }}" class="group relative min-h-[420px] overflow-hidden rounded-3xl bg-gray-950 shadow-xl">
                    <img class="absolute inset-0 h-full w-full object-cover transition duration-700 group-hover:scale-110" src="{{ $relacionado->imagen_url }}" alt="{{ $relacionado->nombre }}">
                    <span class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/10 to-transparent"></span>
                    <span class="absolute inset-x-0 bottom-0 p-7 text-white"><small class="font-bold uppercase tracking-wider text-[#eadfd2]">{{ $relacionado->municipio?->nombre ?: ($relacionado->ubicacion ?: 'Tarija') }}</small><strong class="mt-2 block text-3xl font-black">{{ $relacionado->nombre }}</strong><span class="mt-4 inline-flex items-center gap-2 text-sm font-black">Descubrir <i class="fa-solid fa-arrow-right transition group-hover:translate-x-2"></i></span></span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@if($destino->latitud && $destino->longitud)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    window.initializeDestinationRouteMap?.(
        'destination-route-map',
        @js($rutasLlegada),
        {
            lat: {{ (float) $destino->latitud }},
            lng: {{ (float) $destino->longitud }},
            name: @js($destino->nombre)
        }
    );
});
</script>
@endpush
@endif
