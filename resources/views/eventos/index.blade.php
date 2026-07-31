@extends('layouts.app')

@section('title', 'Eventos')

@section('content')
<section class="bg-pattern pt-32 pb-16 text-white">
    <div class="container-custom">
        <p class="text-sm font-semibold uppercase tracking-wide text-coral-200">Agenda</p>
        <h1 class="mt-3 font-display text-4xl font-black sm:text-5xl">{{ __('portal.events_title') }}</h1>
        <p class="mt-4 max-w-2xl text-white/80">{{ __('portal.events_intro') }}</p>
    </div>
</section>

<section class="py-16">
    <div class="container-custom">
        <form class="mb-8 flex flex-col gap-3 rounded-2xl border border-red-100 bg-white p-4 shadow-lg sm:flex-row sm:items-end" method="GET" action="{{ route('eventos') }}">
            <label class="min-w-0 flex-1"><span class="mb-2 block text-sm font-black uppercase tracking-wider text-red-900">{{ __('portal.filter_municipality') }}</span><select class="w-full rounded-xl border-gray-200 bg-red-50/40 px-4 py-3" name="municipio"><option value="">{{ __('catalog.all_municipalities') }}</option>@foreach($municipalities as $item)<option value="{{ $item->slug }}" @selected($municipality === $item->slug)>{{ $item->nombre }}</option>@endforeach</select></label>
            <button class="btn-primary" type="submit"><i class="fa-solid fa-filter mr-2"></i>{{ __('catalog.filter') }}</button>
            @if($municipality)<a class="inline-flex min-h-12 items-center justify-center rounded-xl border border-gray-200 px-5 font-bold text-gray-700" href="{{ route('eventos') }}">{{ __('portal.clear') }}</a>@endif
        </form>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($eventos as $evento)
                <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-lg transition hover:-translate-y-1 hover:shadow-xl">
                    <img class="mb-5 h-44 w-full rounded-xl object-cover" src="{{ $evento->imagen_url }}" alt="{{ $evento->titulo }}">
                    <div class="flex items-center justify-between">
                        <span class="rounded-full bg-coral-100 px-3 py-1 text-xs font-bold text-coral-700">{{ optional($evento->fecha_inicio)->format('d/m/Y') }}</span>
                        <i class="fa-solid fa-calendar-days text-2xl text-ocean-700"></i>
                    </div>
                    <h2 class="mt-5 text-2xl font-bold">{{ $evento->titulo }}</h2>
                    <p class="mt-3 text-sm leading-6 text-gray-600">{{ $evento->descripcion }}</p>
                    @if($evento->municipio)<p class="mt-4 text-sm font-black text-red-800"><i class="fa-solid fa-map-location-dot mr-2"></i>{{ $evento->municipio->nombre }}</p>@endif
                    <p class="mt-4 text-sm font-semibold text-gray-700"><i class="fa-solid fa-location-dot mr-2 text-coral-500"></i>{{ $evento->lugar ?: $evento->destino?->nombre }}</p>
                    <a class="mt-5 inline-flex font-semibold text-ocean-700" href="{{ route('eventos.show', $evento) }}">{{ __('portal.view_event') }} <i class="fa-solid fa-arrow-right ml-2 mt-1"></i></a>
                </article>
            @empty
                <div class="rounded-2xl bg-white p-8 shadow-sm md:col-span-3">{{ __('portal.no_events') }}</div>
            @endforelse
        </div>
        <div class="mt-10">{{ $eventos->links() }}</div>
    </div>
</section>
@endsection
