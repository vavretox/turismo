@extends('layouts.app')

@section('title', $evento->titulo)

@section('content')
<section class="pt-28 pb-16">
    <div class="container-custom grid gap-10 lg:grid-cols-[1fr_320px]">
        <article class="rounded-md border bg-white p-8 shadow-sm">
            <img class="mb-8 aspect-video w-full rounded-md object-cover" src="{{ $evento->imagen_url }}" alt="{{ $evento->titulo }}">
            <p class="text-sm font-semibold text-coral-700">{{ optional($evento->fecha_inicio)->format('d/m/Y H:i') }}</p>
            <h1 class="mt-3 font-display text-4xl font-bold">{{ $evento->titulo }}</h1>
            <p class="mt-6 text-slate-700">{{ $evento->descripcion }}</p>
        </article>
        <aside class="rounded-md border bg-white p-6 shadow-sm">
            <h2 class="font-bold">Datos del evento</h2>
            <div class="mt-4 space-y-3 text-sm text-slate-700">
                @if($evento->municipio)
                    <p><i class="fa-solid fa-map-location-dot mr-2 text-ocean-700"></i><strong>Municipio:</strong> {{ $evento->municipio->nombre }}</p>
                @endif
                <p><i class="fa-solid fa-location-dot mr-2 text-ocean-700"></i>{{ $evento->lugar ?: 'Lugar por definir' }}</p>
                @if($evento->destino)
                    <p><i class="fa-solid fa-map-pin mr-2 text-ocean-700"></i><a href="{{ route('destinos.show', $evento->destino) }}">{{ $evento->destino->nombre }}</a></p>
                @endif
                @if($evento->fecha_fin)
                    <p><i class="fa-solid fa-calendar-check mr-2 text-ocean-700"></i>Finaliza {{ $evento->fecha_fin->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </aside>
    </div>
</section>
@endsection
