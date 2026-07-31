@extends('layouts.app')

@section('title', 'Vista previa — '.$provider->commercial_name)

@section('content')
<section class="bg-pattern pb-12 pt-32 text-white">
    <div class="container-custom"><p class="text-sm font-black uppercase tracking-[.2em] text-white/65">Vista previa privada</p><h1 class="mt-3 text-4xl font-black">Así verá el turista tu página</h1></div>
</section>
<section class="bg-[#f8f3ec] py-12">
    <div class="container-custom max-w-5xl">
        @if(session('success'))<div class="mb-6 rounded-xl bg-green-50 p-4 font-bold text-green-800">{{ session('success') }}</div>@endif
        <article class="overflow-hidden rounded-3xl bg-white shadow-xl">
            <button class="block h-[360px] w-full cursor-zoom-in" type="button" data-lightbox-image="{{ $place->imagen_url }}" data-lightbox-group="preview-gallery" data-lightbox-caption="{{ $place->titulo }} — Foto principal"><img class="h-full w-full object-cover" src="{{ $place->imagen_url }}" alt="{{ $place->titulo }}"></button>
            <div class="p-7 md:p-10">
                <div class="flex flex-wrap items-center gap-3"><span class="rounded-full bg-red-50 px-4 py-2 text-sm font-black text-red-900">{{ $place->type?->nombre }}</span><span class="text-sm text-gray-500"><i class="fa-solid fa-location-dot mr-1"></i>{{ $place->direccion }}</span></div>
                <h2 class="mt-5 text-4xl font-black text-gray-950">{{ $place->titulo }}</h2>
                <p class="mt-4 text-xl leading-8 text-gray-600">{{ $place->resumen }}</p>
                <div class="mt-7 whitespace-pre-line leading-8 text-gray-700">{{ $place->descripcion }}</div>
                @if($place->service_details)<div class="mt-8 grid gap-3 rounded-2xl bg-[#f8f3ec] p-6">@foreach($place->service_details as $detail)<p><i class="fa-solid fa-circle-check mr-2 text-green-700"></i>{{ $detail }}</p>@endforeach</div>@endif
                @if($place->galeria_urls)<div class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-3">@foreach($place->galeria_urls as $index => $image)<button class="group relative cursor-zoom-in overflow-hidden rounded-2xl" type="button" data-lightbox-image="{{ $image }}" data-lightbox-group="preview-gallery" data-lightbox-caption="{{ $place->titulo }} — Foto {{ $index + 1 }}"><img class="h-48 w-full object-cover transition duration-300 group-hover:scale-105" src="{{ $image }}" alt=""><span class="absolute inset-0 grid place-items-center bg-gray-950/0 text-2xl text-white opacity-0 transition group-hover:bg-gray-950/25 group-hover:opacity-100"><i class="fa-solid fa-magnifying-glass-plus"></i></span></button>@endforeach</div>@endif
                <div class="mt-8 flex flex-wrap gap-3 text-sm">@if($place->telefono)<span class="rounded-full bg-gray-100 px-4 py-2"><i class="fa-solid fa-phone mr-2"></i>{{ $place->telefono }}</span>@endif @if($place->horario)<span class="rounded-full bg-gray-100 px-4 py-2"><i class="fa-solid fa-clock mr-2"></i>{{ $place->horario }}</span>@endif @if($place->precio)<span class="rounded-full bg-gray-100 px-4 py-2"><i class="fa-solid fa-tag mr-2"></i>{{ $place->precio }}</span>@endif</div>
                @if($place->whatsapp || $place->facebook || $place->instagram || $place->tiktok || $place->x_url || $place->youtube_url)<div class="mt-6 flex flex-wrap gap-2 text-xl"><span class="mr-2 self-center text-sm font-black text-gray-500">Redes:</span>@if($place->whatsapp)<i class="fa-brands fa-whatsapp text-green-600"></i>@endif @if($place->facebook)<i class="fa-brands fa-facebook text-blue-700"></i>@endif @if($place->instagram)<i class="fa-brands fa-instagram text-pink-600"></i>@endif @if($place->tiktok)<i class="fa-brands fa-tiktok"></i>@endif @if($place->x_url)<i class="fa-brands fa-x-twitter"></i>@endif @if($place->youtube_url)<i class="fa-brands fa-youtube text-red-700"></i>@endif</div>@endif
            </div>
        </article>
        <div class="mt-7 flex flex-col gap-3 sm:flex-row"><a class="rounded-xl border border-gray-300 bg-white px-6 py-3 text-center font-bold text-gray-700" href="{{ route('prestador.panel') }}"><i class="fa-solid fa-pen mr-2"></i>Volver a editar</a><form method="POST" action="{{ route('prestador.update') }}" class="hidden"></form></div>
    </div>
</section>
@include('partials.image-lightbox')
@endsection
