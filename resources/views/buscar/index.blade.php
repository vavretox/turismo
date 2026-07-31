@extends('layouts.app')

@section('title', 'Buscar')
@section('description', 'Busqueda en destinos, eventos y noticias turisticas.')

@section('content')
<section class="bg-pattern pt-32 pb-16 text-white">
    <div class="container-custom">
        <p class="text-sm font-semibold uppercase tracking-wide text-coral-200">{{ __('portal.search_eyebrow') }}</p>
        <h1 class="mt-3 font-display text-4xl font-black sm:text-5xl">{{ __('portal.search_title') }}</h1>
        <form class="mt-8 flex max-w-2xl gap-3 rounded-2xl bg-white p-2 shadow-xl" action="{{ route('buscar') }}" method="GET">
            <input class="min-w-0 flex-1 rounded-xl border-0 text-gray-900 placeholder:text-gray-400 focus:ring-ocean-500" name="q" type="search" value="{{ $query }}" placeholder="{{ __('portal.search_placeholder') }}">
            <button class="btn-primary" type="submit"><i class="fa-solid fa-search mr-2"></i>{{ __('portal.search') }}</button>
        </form>
    </div>
</section>

<section class="bg-white py-16">
    <div class="container-custom grid gap-12">
        <div>
            <h2 class="section-title">{{ __('ui.destinations') }}</h2>
            <div class="mt-6 grid gap-6 md:grid-cols-3">
                @forelse($destinos as $destino)
                    <a class="content-card" href="{{ route('destinos.show', $destino) }}">
                        <img class="h-48 w-full object-cover" src="{{ $destino->imagen_url }}" alt="{{ $destino->nombre }}">
                        <div class="p-5">
                            <p class="text-sm font-semibold text-coral-700">{{ $destino->municipio?->nombre ?: ($destino->ubicacion ?: $destino->categoria?->nombre) }}</p>
                            <h3 class="mt-2 text-xl font-bold">{{ $destino->nombre }}</h3>
                            <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $destino->resumen }}</p>
                        </div>
                    </a>
                @empty
                    <p class="rounded-2xl bg-gray-50 p-6 text-gray-600">{{ __('portal.no_destination_results') }}</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="section-title">{{ __('ui.events') }}</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @forelse($eventos as $evento)
                    <a class="rounded-2xl border border-gray-100 bg-gray-50 p-5 transition hover:border-ocean-100 hover:bg-ocean-50" href="{{ route('eventos.show', $evento) }}">
                        <p class="text-sm font-semibold text-coral-700">{{ optional($evento->fecha_inicio)->format('d/m/Y H:i') }}</p>
                        <h3 class="mt-2 text-xl font-bold">{{ $evento->titulo }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ $evento->lugar ?: $evento->destino?->nombre }}</p>
                    </a>
                @empty
                    <p class="rounded-2xl bg-gray-50 p-6 text-gray-600">{{ __('portal.no_event_results') }}</p>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="section-title">{{ __('ui.news') }}</h2>
            <div class="mt-6 grid gap-4 md:grid-cols-2">
                @forelse($noticias as $noticia)
                    <a class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg" href="{{ route('noticias.show', $noticia) }}">
                        <p class="text-sm font-semibold text-coral-700">{{ optional($noticia->publicado_en)->format('d/m/Y') }}</p>
                        <h3 class="mt-2 text-xl font-bold">{{ $noticia->titulo }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $noticia->resumen }}</p>
                    </a>
                @empty
                    <p class="rounded-2xl bg-gray-50 p-6 text-gray-600">{{ __('portal.no_news_results') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection
