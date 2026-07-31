@extends('layouts.app')

@section('title', __('ui.destinations'))

@section('content')
<section class="bg-pattern pt-32 pb-16 text-white">
    <div class="container-custom">
        <p class="text-sm font-semibold uppercase tracking-wide text-coral-200">{{ __('catalog.explore') }}</p>
        <h1 class="mt-3 font-display text-4xl font-black sm:text-5xl">{{ __('catalog.destinations_title') }}</h1>
        <p class="mt-4 max-w-2xl text-white/80">{{ __('catalog.destinations_intro') }}</p>
    </div>
</section>

<section class="py-16">
    <div class="container-custom">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
            @forelse($destinos as $destino)
                <article class="content-card">
                    <img class="h-60 w-full object-cover" src="{{ $destino->imagen_url }}" alt="{{ $destino->nombre }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-coral-700">{{ $destino->municipio?->nombre ?: ($destino->ubicacion ?: 'Destino') }}</p>
                            <span class="rounded-full bg-ocean-50 px-3 py-1 text-xs font-bold text-ocean-700">{{ $destino->categoria?->nombre ?? 'General' }}</span>
                        </div>
                        <h2 class="mt-3 text-2xl font-bold">{{ $destino->nombre }}</h2>
                        <p class="mt-3 text-sm leading-6 text-gray-600">{{ $destino->resumen }}</p>
                        <a class="mt-5 inline-flex font-semibold text-ocean-700" href="{{ route('destinos.show', $destino) }}">{{ __('catalog.view_destination') }} <i class="fa-solid fa-arrow-right ml-2 mt-1"></i></a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl bg-white p-8 shadow-sm md:col-span-3">{{ __('catalog.no_destinations') }}</div>
            @endforelse
        </div>
        <div class="mt-10">{{ $destinos->links() }}</div>
    </div>
</section>
@endsection
