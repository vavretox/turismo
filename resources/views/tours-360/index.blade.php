@extends('layouts.app')

@section('title', 'Tours virtuales 360°')
@section('description', 'Recorre Tarija mediante fotografías panorámicas en 360 grados.')

@section('content')
<section class="min-h-screen bg-stone-50 pb-20 pt-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-red-100 px-4 py-2 text-sm font-black text-red-900"><i class="fa-solid fa-vr-cardboard"></i> Experiencias inmersivas</span>
            <h1 class="mt-5 text-4xl font-black text-red-950 sm:text-5xl">Tours virtuales 360°</h1>
            <p class="mt-4 text-lg text-stone-600">Explora espacios y destinos de Tarija desde cualquier lugar.</p>
        </div>

        @if($tours->isEmpty())
            <div class="mx-auto mt-14 max-w-2xl rounded-3xl border border-stone-200 bg-white p-10 text-center shadow-sm">
                <i class="fa-solid fa-panorama text-5xl text-red-800"></i>
                <h2 class="mt-5 text-2xl font-black text-red-950">Próximamente</h2>
                <p class="mt-2 text-stone-600">Estamos preparando nuevas experiencias panorámicas.</p>
            </div>
        @else
            <div class="mt-12 grid gap-7 md:grid-cols-2 lg:grid-cols-3">
                @foreach($tours as $tour)
                    <article class="group overflow-hidden rounded-3xl bg-white shadow-lg shadow-stone-900/10 ring-1 ring-stone-200 transition hover:-translate-y-1 hover:shadow-xl">
                        <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-red-950 to-red-700">
                            @if($tour->cover_image)
                                <img class="h-full w-full object-cover transition duration-500 group-hover:scale-105" src="{{ Storage::disk('public')->url($tour->cover_image) }}" alt="{{ $tour->name }}">
                            @else
                                <div class="grid h-full place-items-center"><i class="fa-solid fa-panorama text-6xl text-white/80"></i></div>
                            @endif
                            <span class="absolute right-4 top-4 rounded-full bg-black/60 px-3 py-1.5 text-xs font-bold text-white backdrop-blur"><i class="fa-solid fa-camera mr-1"></i>{{ $tour->scenes_count }} {{ Str::plural('escena', $tour->scenes_count) }}</span>
                        </div>
                        <div class="p-6">
                            <h2 class="text-2xl font-black text-red-950">{{ $tour->name }}</h2>
                            @if($tour->description)<p class="mt-2 line-clamp-3 text-stone-600">{{ $tour->description }}</p>@endif
                            @if($tour->scenes_count)
                                <a class="mt-6 inline-flex items-center gap-2 rounded-full bg-red-950 px-5 py-3 text-sm font-black text-white transition hover:bg-red-800" href="{{ route('tours-360.show', $tour) }}">Iniciar recorrido <i class="fa-solid fa-arrow-right"></i></a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
