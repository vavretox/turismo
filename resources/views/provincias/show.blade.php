@extends('layouts.app')

@section('title', $municipio->nombre)
@section('description', $municipio->resumen)

@section('content')
@php($galeriaMunicipio = collect([$municipio->imagen_url])->merge($municipio->imagenesSecundariasUrls())->filter()->unique()->values())
<section
    class="relative h-screen min-h-[560px] overflow-hidden pt-20 text-white supports-[height:100svh]:h-[100svh]"
    x-data="{
        images: @js($galeriaMunicipio),
        activeIndex: 0,
        animatePhoto: true,
        timer: null,
        start() { this.stop(); this.timer = setInterval(() => this.show((this.activeIndex + 1) % this.images.length, false), 20000); },
        stop() { if (this.timer) clearInterval(this.timer); },
        show(index, restart = true) {
            this.animatePhoto = false;
            this.activeIndex = index;
            this.$nextTick(() => {
                void this.$refs.photo.offsetWidth;
                this.animatePhoto = true;
            });
            if (restart) this.start();
        }
    }"
    x-init="start()"
>
    <img x-ref="photo" class="absolute inset-0 h-full w-full object-cover" :class="animatePhoto && 'municipality-photo-motion'" :src="images[activeIndex]" src="{{ $municipio->imagen_url }}" alt="{{ $municipio->nombre }}">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>
    <div class="container-custom relative flex h-full items-end pb-10 sm:pb-16 md:pb-20">
        <div class="min-w-0 max-w-4xl">
            <p class="text-sm font-semibold uppercase tracking-wide text-red-100">{{ __('catalog.municipality_of') }} {{ $municipio->provincia }}</p>
            <h1 class="mt-3 font-display text-5xl font-black md:text-7xl">{{ $municipio->nombre }}</h1>
            <p class="mt-3 line-clamp-3 max-w-3xl text-base leading-6 text-white/85 sm:mt-5 sm:text-lg sm:leading-8">{{ $municipio->subtitulo ?: $municipio->resumen }}</p>
        </div>
    </div>
    @if($galeriaMunicipio->count() > 1)
        <div class="municipality-gallery absolute right-3 top-1/2 z-20 flex max-h-[calc(100vh-9rem)] -translate-y-1/2 flex-col gap-2 overflow-y-auto rounded-2xl bg-white/90 p-2 shadow-2xl supports-[height:100svh]:max-h-[calc(100svh-9rem)] sm:right-6" aria-label="Galería de imágenes de {{ $municipio->nombre }}">
            @foreach($galeriaMunicipio as $photoIndex => $foto)
                <button
                    type="button"
                    class="h-14 w-16 shrink-0 overflow-hidden rounded-xl border-2 transition hover:scale-105 focus:outline-none focus:ring-4 focus:ring-white/40 sm:h-16 sm:w-20"
                    :class="activeIndex === {{ $photoIndex }} ? 'border-red-800 ring-2 ring-red-200' : 'border-gray-200'"
                    @click="show({{ $photoIndex }})"
                    aria-label="Mostrar otra imagen de {{ $municipio->nombre }}"
                >
                    <img class="h-full w-full object-cover" src="{{ $foto }}" alt="">
                </button>
            @endforeach
        </div>
    @endif
</section>

<section class="bg-white py-16">
    <div class="container-custom grid gap-10 lg:grid-cols-[1fr_340px]">
        <article>
            <p class="text-sm font-bold uppercase tracking-wide text-red-700">{{ __('catalog.about_municipality') }}</p>
            <h2 class="mt-2 font-display text-4xl font-black text-gray-950">{{ __('catalog.identity_routes') }}</h2>
            <div class="mt-6 space-y-5 text-base leading-8 text-gray-700">
                @foreach(preg_split('/\r\n|\r|\n/', $municipio->descripcion ?: $municipio->resumen) as $paragraph)
                    @if(trim($paragraph))
                        <p>{{ trim($paragraph) }}</p>
                    @endif
                @endforeach
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-2">
                <div class="rounded-2xl bg-red-50 p-6">
                    <h3 class="text-xl font-black text-gray-950">{{ __('catalog.places_interest') }}</h3>
                    <ul class="mt-4 space-y-3 text-sm leading-6 text-gray-700">
                        @foreach($municipio->atractivosLista() as $atractivo)
                            <li class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-red-700"></i><span>{{ $atractivo }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl bg-gray-50 p-6">
                    <h3 class="text-xl font-black text-gray-950">{{ __('catalog.festivals') }}</h3>
                    <ul class="mt-4 space-y-3 text-sm leading-6 text-gray-700">
                        @foreach($municipio->fiestasLista() as $fiesta)
                            <li class="flex gap-3"><i class="fa-solid fa-star mt-1 text-red-700"></i><span>{{ $fiesta }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </article>

        <aside class="h-fit rounded-2xl border border-red-100 bg-white p-6 shadow-xl">
            <h2 class="text-xl font-black text-gray-950">{{ __('catalog.quick_info') }}</h2>
            <div class="mt-5">
                <p class="text-sm font-bold uppercase tracking-wide text-red-700">{{ __('catalog.province') }}</p>
                <p class="mt-3 rounded-full bg-red-50 px-3 py-2 text-sm font-bold text-red-700">{{ $municipio->provincia }}</p>
            </div>
            <div class="mt-6">
                <p class="text-sm font-bold uppercase tracking-wide text-red-700">{{ __('catalog.recommendations') }}</p>
                <ul class="mt-3 space-y-3 text-sm leading-6 text-gray-700">
                    @foreach($municipio->recomendacionesLista() as $item)
                        <li class="flex gap-3"><i class="fa-solid fa-check mt-1 text-red-700"></i><span>{{ $item }}</span></li>
                    @endforeach
                </ul>
            </div>
            <a class="btn-primary mt-7 w-full" href="{{ route('contacto') }}">{{ __('catalog.request_info') }}</a>
            <a class="mt-4 inline-flex min-h-11 w-full items-center justify-center font-semibold text-red-700" href="{{ route('municipios.index') }}"><i class="fa-solid fa-arrow-left mr-2"></i>{{ __('catalog.view_municipalities') }}</a>
        </aside>
    </div>
</section>
@endsection
