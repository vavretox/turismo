@extends('layouts.app')

@section('title', $place->titulo)
@section('description', $place->resumen)

@section('content')
<section class="relative min-h-[560px] overflow-hidden bg-gray-950 text-white">
    <button class="absolute inset-0 h-full w-full cursor-zoom-in" type="button" data-lightbox-image="{{ $place->imagen_url }}" data-lightbox-group="place-gallery" data-lightbox-caption="{{ $place->titulo }} — Foto principal" aria-label="Ampliar foto principal"><img class="h-full w-full object-cover opacity-70" src="{{ $place->imagen_url }}" alt="{{ $place->titulo }}"></button>
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-950/35 to-gray-950/20"></div>
    <div class="container-custom pointer-events-none relative flex min-h-[560px] items-end pb-16 pt-32">
        <div class="max-w-4xl"><span class="rounded-full bg-white/15 px-4 py-2 text-sm font-black backdrop-blur">{{ $place->type?->nombre }}</span><h1 class="mt-5 font-display text-5xl font-black md:text-6xl">{{ $place->titulo }}</h1><p class="mt-5 max-w-3xl text-xl leading-8 text-white/85">{{ $place->resumen }}</p></div>
    </div>
</section>
<section class="bg-[#f8f3ec] py-14">
    <div class="container-custom grid gap-8 lg:grid-cols-[1fr_320px]">
        <div class="rounded-3xl bg-white p-7 shadow-lg md:p-10">
            <div class="whitespace-pre-line text-lg leading-8 text-gray-700">{{ $place->descripcion }}</div>
            @if($place->service_details)<div class="mt-8 grid gap-3 rounded-2xl bg-red-50 p-6">@foreach($place->service_details as $detail)<p><i class="fa-solid fa-circle-check mr-2 text-green-700"></i>{{ $detail }}</p>@endforeach</div>@endif
            @if($place->room_options)<div class="mt-8"><h2 class="text-xl font-black">Opciones disponibles</h2><div class="mt-3 flex flex-wrap gap-2">@foreach($place->room_options as $option)<span class="rounded-full bg-gray-100 px-4 py-2 text-sm font-bold">{{ $option }}</span>@endforeach</div></div>@endif
            @if($place->galeria_urls)<div class="mt-9 grid grid-cols-2 gap-3 md:grid-cols-3">@foreach($place->galeria_urls as $index => $image)<button class="group relative cursor-zoom-in overflow-hidden rounded-2xl" type="button" data-lightbox-image="{{ $image }}" data-lightbox-group="place-gallery" data-lightbox-caption="{{ $place->titulo }} — Foto {{ $index + 1 }}"><img class="h-52 w-full object-cover transition duration-300 group-hover:scale-105" src="{{ $image }}" alt="Galería de {{ $place->titulo }}"><span class="absolute inset-0 grid place-items-center bg-gray-950/0 text-2xl text-white opacity-0 transition group-hover:bg-gray-950/25 group-hover:opacity-100"><i class="fa-solid fa-magnifying-glass-plus"></i></span></button>@endforeach</div>@endif
        </div>
        <aside class="h-fit rounded-3xl bg-white p-6 shadow-lg">
            <h2 class="text-xl font-black">Información</h2>
            <div class="mt-5 space-y-4 text-sm text-gray-700"><p><i class="fa-solid fa-location-dot mr-2 text-red-800"></i>{{ $place->direccion }}</p>@if($place->telefono)<p><i class="fa-solid fa-phone mr-2 text-red-800"></i>{{ $place->telefono }}</p>@endif @if($place->horario)<p><i class="fa-solid fa-clock mr-2 text-red-800"></i>{{ $place->horario }}</p>@endif @if($place->precio)<p><i class="fa-solid fa-tag mr-2 text-red-800"></i>{{ $place->precio }}</p>@endif</div>
            @if($place->sitio_web)<a class="btn-primary mt-6 w-full" href="{{ $place->sitio_web }}" target="_blank" rel="noopener">Visitar sitio web</a>@endif
            @php($whatsappNumber = preg_replace('/\D+/', '', (string) $place->whatsapp))
            @if($whatsappNumber || $place->facebook || $place->instagram || $place->tiktok || $place->x_url || $place->youtube_url)
                <div class="mt-6 border-t border-gray-100 pt-5">
                    <h3 class="text-sm font-black uppercase tracking-wider text-gray-500">Síguenos y contáctanos</h3>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($whatsappNumber)<a class="grid h-11 w-11 place-items-center rounded-full bg-green-600 text-xl text-white transition hover:-translate-y-1" href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>@endif
                        @if($place->facebook)<a class="grid h-11 w-11 place-items-center rounded-full bg-blue-700 text-xl text-white transition hover:-translate-y-1" href="{{ $place->facebook }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>@endif
                        @if($place->instagram)<a class="grid h-11 w-11 place-items-center rounded-full bg-gradient-to-br from-purple-600 via-pink-600 to-amber-500 text-xl text-white transition hover:-translate-y-1" href="{{ $place->instagram }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>@endif
                        @if($place->tiktok)<a class="grid h-11 w-11 place-items-center rounded-full bg-gray-950 text-xl text-white transition hover:-translate-y-1" href="{{ $place->tiktok }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>@endif
                        @if($place->x_url)<a class="grid h-11 w-11 place-items-center rounded-full bg-gray-900 text-xl text-white transition hover:-translate-y-1" href="{{ $place->x_url }}" target="_blank" rel="noopener" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>@endif
                        @if($place->youtube_url)<a class="grid h-11 w-11 place-items-center rounded-full bg-red-700 text-xl text-white transition hover:-translate-y-1" href="{{ $place->youtube_url }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>@endif
                    </div>
                </div>
            @endif
            <a class="mt-5 block text-center text-sm font-bold text-red-800" href="{{ route('mapa.interactivo') }}">Volver al mapa</a>
        </aside>
    </div>
</section>
@if($place->serviceProvider && $place->serviceProvider->offerings->where('activo', true)->isNotEmpty())
@php($contentLabels = $place->serviceProvider->contentLabels())
<section class="bg-white py-14">
    <div class="container-custom">
        <div class="mb-8"><p class="text-sm font-black uppercase tracking-[.18em] text-red-800">Descubre lo que ofrecemos</p><h2 class="mt-2 text-3xl font-black text-gray-950">{{ $contentLabels[0] }}</h2></div>
        <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-3">
            @foreach($place->serviceProvider->offerings->where('activo', true) as $offering)
                <article class="overflow-hidden rounded-3xl border border-gray-100 bg-[#f8f3ec] shadow-lg">
                    <button class="group relative block h-60 w-full cursor-zoom-in overflow-hidden" type="button" data-lightbox-image="{{ $offering->imagen_url }}" data-lightbox-group="offering-{{ $offering->id }}" data-lightbox-caption="{{ $offering->titulo }}"><img class="h-full w-full object-cover transition duration-300 group-hover:scale-105" src="{{ $offering->imagen_url }}" alt="{{ $offering->titulo }}"><span class="absolute inset-0 grid place-items-center bg-gray-950/0 text-2xl text-white opacity-0 transition group-hover:bg-gray-950/25 group-hover:opacity-100"><i class="fa-solid fa-magnifying-glass-plus"></i></span></button>
                    @foreach($offering->galeria_urls as $galleryImage)<button class="hidden" type="button" data-lightbox-image="{{ $galleryImage }}" data-lightbox-group="offering-{{ $offering->id }}" data-lightbox-caption="{{ $offering->titulo }}"></button>@endforeach
                    <div class="p-6"><h3 class="text-2xl font-black text-gray-950">{{ $offering->titulo }}</h3><p class="mt-3 leading-7 text-gray-600">{{ $offering->resumen }}</p>@if($offering->descripcion)<p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700">{{ $offering->descripcion }}</p>@endif<div class="mt-5 flex flex-wrap gap-2">@if($offering->duracion)<span class="rounded-full bg-white px-3 py-2 text-xs font-bold"><i class="fa-solid fa-clock mr-1 text-red-800"></i>{{ $offering->duracion }}</span>@endif @if($offering->precio)<span class="rounded-full bg-white px-3 py-2 text-xs font-bold"><i class="fa-solid fa-tag mr-1 text-red-800"></i>{{ $offering->precio }}</span>@endif</div>@if($offering->incluye)<div class="mt-5 rounded-2xl bg-white p-4 text-sm leading-6 text-gray-700"><strong class="block text-red-900">{{ $contentLabels[4] }}</strong><p class="mt-1 whitespace-pre-line">{{ $offering->incluye }}</p></div>@endif @if(count($offering->galeria_urls))<p class="mt-4 text-xs font-bold text-gray-500"><i class="fa-solid fa-images mr-1"></i>{{ count($offering->galeria_urls) + 1 }} fotografías · toca la imagen para verlas</p>@endif</div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@include('partials.image-lightbox')
@endsection
