@extends('layouts.app')

@section('title', __('home.meta.title'))
@section('description', __('home.meta.description'))

@section('content')
    @php
        $heroSlides = $heroImages->map(fn ($image) => [
            'imagen' => $image->imagen_url,
            'titulo' => $image->nombre,
            'descripcion' => $image->descripcion ?: __('home.hero.default_description'),
        ])->values();
        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([[
                'imagen' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1920&q=85',
                'titulo' => __('home.hero.default_title'),
                'descripcion' => __('home.hero.default_description'),
            ]]);
        }
    @endphp

    <section class="bg-white">
        <div
            class="relative h-screen min-h-[560px] overflow-hidden text-white supports-[height:100svh]:h-[100svh]"
            x-data="{ active: 0, total: {{ $heroSlides->count() }}, timer: null, start() { clearInterval(this.timer); this.timer = setInterval(() => this.active = (this.active + 1) % this.total, 30000) }, go(index) { this.active = index; this.start() } }"
            x-init="start()"
        >
            @foreach($heroSlides as $index => $slide)
                <div class="absolute inset-0" @if($index > 0) x-cloak @endif x-show="active === {{ $index }}" x-transition:enter="transition-opacity duration-1000" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-1000" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <img class="hero-slide-image absolute inset-0 h-full w-full object-cover" src="{{ $slide['imagen'] }}" alt="{{ $slide['titulo'] }}">
                </div>
            @endforeach
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950/45 via-gray-950/25 to-gray-950/75"></div>
            <div class="container-custom relative flex h-full items-center justify-center pt-20 text-center">
                <div class="max-w-4xl">
                    <p class="mb-4 inline-flex items-center rounded-full bg-white/15 px-4 py-2 text-sm font-semibold backdrop-blur">
                        <i class="fa-solid fa-location-dot mr-2 text-coral-300"></i>{{ __('home.hero.badge') }}
                    </p>
                    @foreach($heroSlides as $index => $slide)
                        <div @if($index > 0) x-cloak @endif x-show="active === {{ $index }}">
                            <h1 class="font-display text-4xl font-black leading-tight md:text-6xl">{{ $slide['titulo'] }}</h1>
                            <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-white/85">{{ $slide['descripcion'] }}</p>
                        </div>
                    @endforeach
                    <div class="mt-8 flex flex-wrap justify-center gap-3 text-sm font-bold">
                        <a class="hero-pill" href="{{ route('municipios.index') }}"><i class="fa-solid fa-map-location-dot"></i> {{ __('home.hero.municipalities') }}</a>
                        <a class="hero-pill" href="{{ route('destinos') }}"><i class="fa-solid fa-location-dot"></i> {{ __('home.hero.destinations') }}</a>
                        <a class="hero-pill" href="{{ route('eventos') }}"><i class="fa-solid fa-calendar-days"></i> {{ __('home.hero.events') }}</a>
                    </div>
                </div>
            </div>
            @if($heroSlides->count() > 1)
                <div class="hero-journey" aria-label="Recorrido de imágenes de portada">
                    @foreach($heroSlides as $index => $slide)
                        <button type="button" class="hero-journey-item" :class="active === {{ $index }} ? 'is-active' : ''" @click="go({{ $index }})" aria-label="Mostrar {{ $slide['titulo'] }}">
                            <img src="{{ $slide['imagen'] }}" alt="">
                            <span class="hero-journey-progress"><span></span></span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="container-custom relative z-10 -mt-8">
            <div class="mx-auto max-w-4xl rounded-md bg-white px-6 py-8 text-center shadow-xl md:px-12">
                <p class="text-xl font-black leading-8 text-gray-950 md:text-2xl">
                    {{ __('home.intro.text') }}
                </p>
                <div class="mt-6 flex flex-col justify-center gap-3 sm:flex-row">
                    <a class="btn-primary" href="{{ route('destinos') }}"><i class="fa-solid fa-search mr-2"></i>{{ __('home.intro.explore') }}</a>
                    <a class="rounded-lg border border-gray-200 px-6 py-3 font-semibold text-gray-900 transition hover:border-ocean-200 hover:bg-ocean-50 hover:text-ocean-700" href="{{ route('mapa.interactivo') }}"><i class="fa-solid fa-map mr-2"></i>{{ __('home.intro.map') }}</a>
                </div>
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden bg-[#fff7f4] py-24 pt-28">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-white to-transparent"></div>
            <div class="absolute -left-24 top-24 h-72 w-72 rounded-full bg-red-200/45 blur-3xl"></div>
            <div class="absolute right-0 top-12 h-56 w-1/2 bg-gradient-to-l from-red-100/70 via-white/30 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-white/80 to-transparent"></div>
        </div>
        <div class="relative mx-auto w-[min(96%,1600px)]">
            <div class="mb-10 flex flex-col justify-between gap-4 px-2 md:flex-row md:items-end">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <h2 class="section-title">{{ __('home.destinations.title') }} <span class="text-gradient">{{ __('home.destinations.highlight') }}</span></h2>
                    <p class="section-subtitle">{{ __('home.destinations.subtitle') }}</p>
                </div>
                </div>
                <a class="inline-flex items-center gap-2 font-bold text-red-800" href="{{ route('destinos') }}">{{ __('home.common.view_all') }} <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            @php
                $destinosAdministrables = $destinos->map(fn ($destino) => [
                    'nombre' => $destino->nombre,
                    'ubicacion' => $destino->municipio?->nombre ?: ($destino->ubicacion ?: 'Tarija'),
                    'resumen' => $destino->resumen,
                    'imagen' => $destino->imagen_url,
                    'url' => route('destinos.show', $destino),
                ]);
                $destinosRespaldo = collect([
                    ['nombre' => 'Ruta del Vino', 'ubicacion' => 'Uriondo', 'resumen' => 'Bodegas, vinedos, singanis y gastronomia chapaca.', 'imagen' => 'https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=1400&q=85', 'url' => route('destinos')],
                    ['nombre' => 'Casa Vieja', 'ubicacion' => 'San Lorenzo', 'resumen' => 'Historia, arquitectura tradicional y cultura tarijena.', 'imagen' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?auto=format&fit=crop&w=1400&q=85', 'url' => route('destinos')],
                    ['nombre' => 'Reserva de Sama', 'ubicacion' => 'Tarija', 'resumen' => 'Paisajes altoandinos, lagunas y miradores naturales.', 'imagen' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1400&q=85', 'url' => route('destinos')],
                    ['nombre' => 'Entre Rios Natural', 'ubicacion' => 'Entre Rios', 'resumen' => 'Rios, serranias, bosques y cultura viva para descubrir.', 'imagen' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1400&q=85', 'url' => route('destinos')],
                ]);
                $nombresAdministrables = $destinosAdministrables->pluck('nombre');
                $destinosGaleria = $destinosAdministrables
                    ->concat($destinosRespaldo->reject(fn ($item) => $nombresAdministrables->contains($item['nombre'])))
                    ->take(4)->values();
                $destinoPrincipal = $destinosGaleria->first();
            @endphp
            <div class="destination-showcase">
                <div class="destination-showcase-hero">
                    <img src="{{ $destinoPrincipal['imagen'] }}" alt="{{ $destinoPrincipal['nombre'] }}">
                    <div class="destination-showcase-overlay"></div>
                    <div class="destination-showcase-copy">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-xs font-black uppercase tracking-widest backdrop-blur"><i class="fa-solid fa-location-dot text-[#eadfd2]"></i>{{ $destinoPrincipal['ubicacion'] }}</span>
                        <h3>{{ $destinoPrincipal['nombre'] }}</h3>
                        <p>{{ $destinoPrincipal['resumen'] }}</p>
                        <a href="{{ $destinoPrincipal['url'] }}">{{ __('home.destinations.discover') }} <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="destination-showcase-grid">
                    @foreach($destinosGaleria as $item)
                        <a class="destination-showcase-card group" href="{{ $item['url'] }}">
                            <img src="{{ $item['imagen'] }}" alt="{{ $item['nombre'] }}">
                            <span class="destination-card-shade"></span>
                            <span class="destination-card-content">
                                <small>{{ $item['ubicacion'] }}</small>
                                <strong>{{ $item['nombre'] }}</strong>
                                <span>{{ $item['resumen'] }}</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-pattern relative overflow-hidden py-20 text-white">
        <div class="container-custom">
            <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4 xl:gap-8">
                <div class="feature-card">
                    <i class="fa-solid fa-map-marked-alt text-3xl text-[#eadfd2]"></i>
                    <h3 class="mt-4 text-xl font-bold">{{ __('home.features.destinations_title') }}</h3>
                    <p class="mt-2 text-sm text-white/75">{{ __('home.features.destinations_text') }}</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-calendar-check text-3xl text-[#eadfd2]"></i>
                    <h3 class="mt-4 text-xl font-bold">{{ __('home.features.agenda_title') }}</h3>
                    <p class="mt-2 text-sm text-white/75">{{ __('home.features.agenda_text') }}</p>
                </div>
                <div class="feature-card">
                    <i class="fa-solid fa-newspaper text-3xl text-[#eadfd2]"></i>
                    <h3 class="mt-4 text-xl font-bold">{{ __('home.features.news_title') }}</h3>
                    <p class="mt-2 text-sm text-white/75">{{ __('home.features.news_text') }}</p>
                </div>
                <div class="feature-card transition hover:-translate-y-1 hover:bg-white/15">
                    <i class="fa-solid fa-clipboard-list text-3xl text-[#eadfd2]"></i>
                    <h3 class="mt-4 text-xl font-bold">{{ __('home.features.providers_title') }}</h3>
                    <p class="mt-2 text-sm text-white/75">{{ __('home.features.providers_text') }}</p>
                    <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm font-bold text-[#eadfd2]">
                        <a class="group" href="{{ route('prestadores.create') }}" target="_blank" rel="noopener noreferrer">{{ __('home.features.providers_form') }} <i class="fa-solid fa-arrow-right ml-1 transition group-hover:translate-x-1"></i></a>
                        <a class="underline decoration-white/35 underline-offset-4" href="{{ route('prestadores.index') }}" target="_blank" rel="noopener noreferrer">{{ __('home.features.providers_status') }} <i class="fa-solid fa-magnifying-glass ml-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="relative overflow-hidden bg-[#f8f3ec] py-24" x-data="{ activeProvince: null, pinnedProvince: null, selectProvince(slug) { this.pinnedProvince = this.pinnedProvince === slug ? null : slug; this.activeProvince = this.pinnedProvince; } }">
        @php
            $provincePalettes = [
                'tarija' => ['#7f1d2d', '#d8c8b8', 'fa-landmark'],
                'uriondo' => ['#6f1d2c', '#c9aaa7', 'fa-wine-bottle'],
                'yunchara' => ['#075985', '#93c5fd', 'fa-mountain-sun'],
                'san-lorenzo' => ['#355c4a', '#b8c0ae', 'fa-hat-cowboy'],
                'el-puente' => ['#285943', '#c0b6a4', 'fa-bridge'],
                'padcaya' => ['#991b1b', '#f59e0b', 'fa-tree'],
            ];
        @endphp
        <div class="pointer-events-none absolute inset-0 opacity-40" style="background: radial-gradient(circle at 10% 20%, #e7ddd0 0, transparent 22%), radial-gradient(circle at 90% 75%, #9eb1a3 0, transparent 24%);"></div>
        <div class="container-custom relative">
            <div class="mx-auto mb-12 max-w-3xl text-center">
                <p class="text-sm font-black uppercase tracking-[.22em] text-red-800">{{ __('home.municipalities.eyebrow') }}</p>
                <h2 class="section-title mt-3">{{ __('home.municipalities.title') }} <span class="text-gradient">{{ __('home.municipalities.highlight') }}</span></h2>
                <p class="section-subtitle mx-auto">{{ __('home.municipalities.subtitle') }}</p>
            </div>
            <div class="municipality-experience-nav" aria-label="Selección animada de municipios destacados">
                @foreach($municipiosTuristicos as $municipioTab)
                    @php($tabPalette = $provincePalettes[$municipioTab->slug] ?? ['#6f1d2c', '#d8c8b8', 'fa-location-dot'])
                    <button
                        type="button"
                        class="municipality-experience-tab"
                        :class="activeProvince === '{{ $municipioTab->slug }}' && 'is-active'"
                        @mouseenter="activeProvince = '{{ $municipioTab->slug }}'"
                        @mouseleave="activeProvince = pinnedProvince"
                        @click="selectProvince('{{ $municipioTab->slug }}')"
                    >
                        <span><i class="fa-solid {{ $tabPalette[2] }}"></i></span>
                        <strong>{{ $municipioTab->nombre }}</strong>
                        <small>{{ $municipioTab->provincia }}</small>
                    </button>
                @endforeach
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" @mouseleave="activeProvince = pinnedProvince">
                @foreach($municipiosTuristicos as $province)
                    @php($palette = $provincePalettes[$province->slug] ?? ['#6f1d2c', '#d8c8b8', 'fa-location-dot'])
                    <a
                        class="province-card group"
                        :class="{ 'is-selected': activeProvince === '{{ $province->slug }}', 'is-muted': activeProvince && activeProvince !== '{{ $province->slug }}' }"
                        style="--province-primary: {{ $palette[0] }}; --province-accent: {{ $palette[1] }};"
                        href="{{ route('municipios.show', $province) }}"
                        @mouseenter="activeProvince = '{{ $province->slug }}'"
                        @mouseleave="activeProvince = pinnedProvince"
                        @focus="activeProvince = '{{ $province->slug }}'"
                    >
                        <span class="province-color-band"></span>
                        <span class="province-photo-shell">
                            <img src="{{ $province->imagen_url }}" alt="Paisaje turístico de {{ $province->nombre }}">
                            <span class="province-photo-overlay"></span>
                            <span class="province-icon"><i class="fa-solid {{ $palette[2] }}"></i></span>
                        </span>
                        <span class="block min-w-0 p-5 pt-4">
                            <span class="block text-xs font-black uppercase tracking-wider text-gray-500">{{ __('home.municipalities.municipality_of') }} {{ $province->provincia }}</span>
                            <span class="mt-1 block text-xl font-black text-gray-950">{{ $province->nombre }}</span>
                            <span class="mt-2 block text-sm leading-6 text-gray-600">{{ $province->resumen ?: $province->subtitulo }}</span>
                            <span class="province-more"><i class="fa-solid fa-location-dot"></i> {{ __('home.municipalities.discover') }} <i class="fa-solid fa-arrow-right ml-auto"></i></span>
                        </span>
                    </a>
                @endforeach
            </div>
            <div class="mt-10 flex justify-center">
                <a class="btn-primary min-w-[220px] gap-3 rounded-full" href="{{ route('municipios.index') }}">
                    {{ __('home.municipalities.view_all') }} <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Sección Servicios Turísticos desactivada temporalmente.
         Para volver a utilizarla, elimina este comentario Blade de apertura y el cierre ubicado después de la sección.
    <section id="servicios-turisticos" class="relative overflow-hidden bg-white py-20">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-[#fff7f4] to-gray-50"></div>
        <div class="pointer-events-none absolute -left-32 top-20 h-80 w-80 rounded-full bg-red-100 blur-3xl"></div>
        <div class="pointer-events-none absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-red-50 to-transparent"></div>
        <div class="container-custom relative">
            <div class="mb-12 flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <div class="mb-4 h-12 w-1 rounded-full bg-red-800"></div>
                    <h2 class="section-title">Servicios <span class="text-gradient">Turisticos</span></h2>
                    <p class="section-subtitle">Contrata servicios habilitados para planificar experiencias, movilidad, hospedaje y recorridos.</p>
                </div>
                <a class="hidden rounded-full bg-red-950 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-red-950/15 transition hover:bg-red-800 md:inline-flex" href="{{ route('mapa.interactivo') }}">Ver mapa turístico</a>
            </div>
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-5">
                @foreach($serviciosTuristicos as $slug => $servicio)
                    <a class="service-card group" href="{{ route('servicios.show', $slug) }}">
                        <div class="service-pin mx-auto" style="--pin-color: {{ $servicio['color'] }}; --pin-accent: {{ $servicio['accent'] }};">
                            <span>{{ $servicio['sigla'] }}</span>
                        </div>
                        <h3 class="mt-7 text-center text-lg font-black text-gray-900">{{ $servicio['titulo'] }}</h3>
                        <p class="mt-3 text-center text-sm leading-6 text-gray-600">{{ $servicio['subtitulo'] }}</p>
                        <div class="service-details">
                            @foreach(array_slice($servicio['items'], 0, 2) as $item)
                                <span><i class="fa-solid fa-check"></i>{{ $item }}</span>
                            @endforeach
                        </div>
                        <span class="mt-4 flex items-center justify-center gap-2 text-sm font-black text-red-800">Explorar <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    --}}

    {{-- Mapa ilustrado de provincias desactivado temporalmente.
         Para recuperarlo, elimina este comentario Blade de apertura y el cierre situado antes del mapa libre.
    <section id="mapa" class="bg-gray-50 py-20">
        <div class="container-custom">
            <div class="mb-10 text-center">
                <h2 class="section-title">Mapa <span class="text-gradient">interactivo</span></h2>
                <p class="section-subtitle mx-auto">Una referencia geografica para planificar rutas turisticas.</p>
            </div>
            <div class="grid gap-8 rounded-3xl bg-[#fff4ef] p-6 shadow-xl shadow-red-950/10 lg:grid-cols-[1fr_340px] lg:p-8">
                <div class="overflow-hidden">
                    <div
                        id="tarija-illustrated-map"
                        class="min-h-[520px]"
                        data-destinos-url="{{ route('destinos') }}"
                        aria-label="Mapa interactivo de municipios de Tarija"
                    >
                        <div class="px-4 pb-2 text-center">
                            <h3 class="font-display text-4xl font-black text-[#6b0f1a] md:text-5xl">Explora Tarija</h3>
                        </div>
                        <div class="mx-auto max-w-5xl px-2 pb-2 pt-5">
                            <div class="relative overflow-hidden">
                                <img class="tarija-province-map block h-auto w-full" src="{{ asset('images/referencia/tarija1.png') }}" alt="Mapa de provincias del departamento de Tarija">
                                <a class="tarija-province-label" data-provincia="Eustaquio Mendez" href="{{ route('municipios.show', 'san-lorenzo') }}" style="left: 18%; top: 19%;" aria-label="Eustaquio Mendez">Eustaquio Mendez</a>
                                <a class="tarija-province-label" data-provincia="Cercado" href="{{ route('municipios.show', 'tarija') }}" style="left: 28%; top: 34%;" aria-label="Cercado">Cercado</a>
                                <a class="tarija-province-label" data-provincia="Jose Maria Aviles" href="{{ route('municipios.show', 'uriondo') }}" style="left: 17%; top: 48%;" aria-label="Jose Maria Aviles">Jose Maria Aviles</a>
                                <a class="tarija-province-label" data-provincia="Aniceto Arce" href="{{ route('municipios.show', 'padcaya') }}" style="left: 29%; top: 60%;" aria-label="Aniceto Arce">Aniceto Arce</a>
                                <a class="tarija-province-label" data-provincia="Burdet O'Connor" href="{{ route('municipios.show', 'entre-rios') }}" style="left: 43%; top: 25%;" aria-label="Burdet O'Connor">Burdet O'Connor</a>
                                <a class="tarija-province-label" data-provincia="Gran Chaco" href="{{ route('municipios.show', 'yacuiba') }}" style="left: 70%; top: 30%;" aria-label="Gran Chaco">Gran Chaco</a>
                            </div>
                        </div>
                    </div>
                </div>
                <aside id="municipio-info" class="self-center lg:py-8">
                    <p class="text-sm font-semibold uppercase tracking-wide text-red-700">Provincias</p>
                    <h3 class="mt-2 text-2xl font-black text-[#4a0711]">Listado de provincias</h3>
                    <div class="mt-6 grid gap-3">
                        <a class="group flex items-center justify-between rounded-xl bg-white/75 px-4 py-3 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-red-950/5 transition hover:bg-white hover:text-red-800" href="{{ route('municipios.show', 'tarija') }}">
                            <span>Cercado</span><i class="fa-solid fa-arrow-right text-red-700 transition group-hover:translate-x-1"></i>
                        </a>
                        <a class="group flex items-center justify-between rounded-xl bg-white/75 px-4 py-3 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-red-950/5 transition hover:bg-white hover:text-red-800" href="{{ route('municipios.show', 'uriondo') }}">
                            <span>Jose Maria Aviles</span><i class="fa-solid fa-arrow-right text-red-700 transition group-hover:translate-x-1"></i>
                        </a>
                        <a class="group flex items-center justify-between rounded-xl bg-white/75 px-4 py-3 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-red-950/5 transition hover:bg-white hover:text-red-800" href="{{ route('municipios.show', 'san-lorenzo') }}">
                            <span>Eustaquio Mendez</span><i class="fa-solid fa-arrow-right text-red-700 transition group-hover:translate-x-1"></i>
                        </a>
                        <a class="group flex items-center justify-between rounded-xl bg-white/75 px-4 py-3 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-red-950/5 transition hover:bg-white hover:text-red-800" href="{{ route('municipios.show', 'padcaya') }}">
                            <span>Aniceto Arce</span><i class="fa-solid fa-arrow-right text-red-700 transition group-hover:translate-x-1"></i>
                        </a>
                        <a class="group flex items-center justify-between rounded-xl bg-white/75 px-4 py-3 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-red-950/5 transition hover:bg-white hover:text-red-800" href="{{ route('municipios.show', 'entre-rios') }}">
                            <span>Burdet O'Connor</span><i class="fa-solid fa-arrow-right text-red-700 transition group-hover:translate-x-1"></i>
                        </a>
                        <a class="group flex items-center justify-between rounded-xl bg-white/75 px-4 py-3 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-red-950/5 transition hover:bg-white hover:text-red-800" href="{{ route('municipios.show', 'yacuiba') }}">
                            <span>Gran Chaco</span><i class="fa-solid fa-arrow-right text-red-700 transition group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
    --}}

    <section id="google-maps-turistico" class="relative overflow-hidden bg-white py-20">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-white via-red-50/30 to-[#f3ede5]"></div>
        <div class="relative mx-auto w-[min(96%,1600px)]">
            <div class="mb-10 flex flex-col justify-between gap-4 lg:flex-row lg:items-end">
                <div>
                    <p class="text-sm font-black uppercase tracking-[.2em] text-red-700">{{ __('home.map.eyebrow') }}</p>
                    <h2 class="section-title mt-2">{{ __('home.map.title') }} <span class="text-gradient">{{ __('home.map.highlight') }}</span></h2>
                    <p class="section-subtitle">{{ __('home.map.subtitle') }}</p>
                </div>
                <a class="home-map-button inline-flex w-fit items-center gap-2 rounded-full bg-red-950 px-5 py-3 text-sm font-black text-white shadow-lg transition hover:-translate-y-1 hover:bg-red-800" href="{{ route('mapa.interactivo') }}">{{ __('home.map.open') }} <i class="fa-solid fa-up-right-from-square"></i></a>
            </div>
            <div class="home-map-embed overflow-hidden rounded-[30px] border border-red-100 bg-white shadow-2xl shadow-red-950/15">
                <iframe
                    class="block h-[460px] w-full border-0 sm:h-[560px] lg:h-[680px]"
                    src="{{ route('mapa.interactivo', ['embed' => 1]) }}"
                    title="{{ __('home.map.frame_title') }}"
                    loading="lazy"
                ></iframe>
            </div>
        </div>
    </section>

    <section class="bg-white py-20">
        <div class="container-custom grid gap-12 lg:grid-cols-2">
            <div>
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <h2 class="section-title">{{ __('home.events.title') }}</h2>
                        <p class="section-subtitle">{{ __('home.events.subtitle') }}</p>
                    </div>
                    <a class="hidden font-semibold text-ocean-700 sm:inline-flex" href="{{ route('eventos') }}">{{ __('home.events.view_agenda') }}</a>
                </div>
                <div class="grid gap-4">
                    @forelse($eventos as $evento)
                        <a class="rounded-2xl border border-gray-100 bg-gray-50 p-5 transition hover:border-ocean-100 hover:bg-ocean-50" href="{{ route('eventos.show', $evento) }}">
                            <p class="text-sm font-semibold text-coral-700">{{ optional($evento->fecha_inicio)->format('d/m/Y H:i') }}</p>
                            <h3 class="mt-2 text-xl font-bold">{{ $evento->titulo }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ $evento->lugar ?: $evento->destino?->nombre }}</p>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-gray-50 p-6 text-gray-600">{{ __('home.events.empty') }}</div>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <h2 class="section-title">{{ __('home.news.title') }}</h2>
                        <p class="section-subtitle">{{ __('home.news.subtitle') }}</p>
                    </div>
                    <a class="hidden font-semibold text-ocean-700 sm:inline-flex" href="{{ route('noticias') }}">{{ __('home.news.view_news') }}</a>
                </div>
                <div class="grid gap-4">
                    @forelse($noticias as $noticia)
                        <a class="grid gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-lg sm:grid-cols-[120px_1fr]" href="{{ route('noticias.show', $noticia) }}">
                            <img class="h-28 w-full rounded-xl object-cover" src="{{ $noticia->imagen_url }}" alt="{{ $noticia->titulo }}">
                            <div>
                                <p class="text-sm font-semibold text-coral-700">{{ optional($noticia->publicado_en)->format('d/m/Y') }}</p>
                                <h3 class="mt-1 text-lg font-bold">{{ $noticia->titulo }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $noticia->resumen }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-gray-50 p-6 text-gray-600">{{ __('home.news.empty') }}</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="bg-ocean-700 py-20 text-white">
        <div class="container-custom">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-black uppercase tracking-[.2em] text-white/65">{{ __('home.community.eyebrow') }}</p>
                <h2 class="mt-3 font-display text-4xl font-black md:text-5xl">{{ __('home.community.title') }}</h2>
                <p class="mx-auto mt-5 max-w-2xl text-lg leading-8 text-white/80">
                    {{ __('home.community.subtitle') }}
                </p>
            </div>

            <div class="mx-auto mt-10 grid max-w-6xl gap-5 md:grid-cols-3">
                <a class="group rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur transition hover:-translate-y-1 hover:bg-white/15" href="{{ route('actividades.index') }}">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-ocean-700"><i class="fa-solid fa-calendar-week"></i></span>
                    <h3 class="mt-5 text-xl font-black">{{ __('home.community.activities_title') }}</h3>
                    <p class="mt-2 text-sm leading-6 text-white/75">{{ __('home.community.activities_text') }}</p>
                    <span class="mt-5 inline-flex items-center gap-2 font-bold">{{ __('home.community.activities_action') }} <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
                </a>

                <a class="group rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur transition hover:-translate-y-1 hover:bg-white/15" href="{{ route('servicios.index') }}">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-ocean-700"><i class="fa-solid fa-compass"></i></span>
                    <h3 class="mt-5 text-xl font-black">{{ __('home.community.services_title') }}</h3>
                    <p class="mt-2 text-sm leading-6 text-white/75">{{ __('home.community.services_text') }}</p>
                    <span class="mt-5 inline-flex items-center gap-2 font-bold">{{ __('home.community.services_action') }} <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
                </a>

                <a class="group rounded-2xl border border-white/15 bg-white/10 p-6 backdrop-blur transition hover:-translate-y-1 hover:bg-white/15" href="{{ route('contacto', ['motivo' => 'experiencia']) }}">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-xl text-ocean-700"><i class="fa-solid fa-comments"></i></span>
                    <h3 class="mt-5 text-xl font-black">{{ __('home.community.share_title') }}</h3>
                    <p class="mt-2 text-sm leading-6 text-white/75">{{ __('home.community.share_text') }}</p>
                    <span class="mt-5 inline-flex items-center gap-2 font-bold">{{ __('home.community.share_action') }} <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
                </a>
            </div>
        </div>
    </section>
@endsection
