@extends('layouts.app')

@section('title', __('catalog.activities_meta'))
@section('description', 'Consulta las próximas actividades turísticas y filtra la agenda por fechas.')

@section('content')
<section class="bg-gradient-to-br from-red-950 via-red-900 to-amber-900 pb-16 pt-32 text-white">
    <div class="container-custom">
        <span class="text-sm font-black uppercase tracking-[.2em] text-amber-300">{{ __('catalog.weekly_agenda') }}</span>
        <h1 class="mt-4 text-4xl font-black md:text-6xl">{{ __('catalog.activities_title') }}</h1>
        <p class="mt-4 max-w-3xl text-lg text-white/80">{{ __('catalog.activities_intro') }}</p>
    </div>
</section>

<section class="bg-[#f8f3ec] py-14">
    <div class="container-custom">
        <form class="grid gap-4 rounded-3xl border border-red-100 bg-white p-6 shadow-xl shadow-red-950/10 md:grid-cols-2 xl:grid-cols-[1.15fr_1fr_1fr_auto_auto] xl:items-end" method="GET" action="{{ route('actividades.index') }}">
            <label><span class="mb-2 block text-sm font-black uppercase tracking-wider text-red-900">{{ __('catalog.municipality') }}</span><select class="w-full rounded-2xl border-gray-200 bg-red-50/40 px-4 py-3 focus:border-red-500 focus:ring-red-200" name="municipio"><option value="">{{ __('catalog.all_municipalities') }}</option>@foreach($municipalities as $item)<option value="{{ $item->slug }}" @selected($municipality === $item->slug)>{{ $item->nombre }}</option>@endforeach</select></label>
            <label><span class="mb-2 block text-sm font-black uppercase tracking-wider text-red-900">{{ __('catalog.from') }}</span><input class="w-full rounded-2xl border-gray-200 bg-red-50/40 px-4 py-3 focus:border-red-500 focus:ring-red-200" type="date" name="desde" value="{{ $from }}"></label>
            <label><span class="mb-2 block text-sm font-black uppercase tracking-wider text-red-900">{{ __('catalog.to') }}</span><input class="w-full rounded-2xl border-gray-200 bg-red-50/40 px-4 py-3 focus:border-red-500 focus:ring-red-200" type="date" name="hasta" value="{{ $to }}"></label>
            <button class="rounded-full bg-red-800 px-6 py-3 font-black text-white shadow-lg hover:bg-red-700" type="submit"><i class="fa-solid fa-filter mr-2"></i>{{ __('catalog.filter') }}</button>
            <a class="rounded-full bg-amber-100 px-6 py-3 text-center font-black text-red-900 hover:bg-amber-200" href="{{ route('actividades.index') }}">{{ __('catalog.current') }}</a>
        </form>

        <div class="mt-10 flex items-end justify-between gap-4">
            <div><p class="text-sm font-black uppercase tracking-wider text-red-700">{{ __('catalog.results') }}</p><h2 class="mt-2 text-3xl font-black text-gray-950">{{ __('catalog.activities_count', ['count' => $activities->count()]) }}</h2></div>
        </div>

        @if($activities->isEmpty())
            <div class="mt-7 rounded-3xl border border-dashed border-red-200 bg-white p-12 text-center"><i class="fa-regular fa-calendar-xmark text-5xl text-red-700"></i><h3 class="mt-4 text-2xl font-black">{{ __('catalog.no_activities') }}</h3><p class="mt-2 text-gray-600">{{ __('catalog.expand_range') }}</p></div>
        @else
            <div class="mt-7 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($activities as $activity)
                    <a class="group overflow-hidden rounded-3xl border border-red-100 bg-white shadow-lg shadow-red-950/10 transition hover:-translate-y-1 hover:shadow-2xl" href="{{ route('actividades.show', $activity) }}">
                        <div class="relative">
                            <img class="h-56 w-full object-cover transition duration-500 group-hover:scale-105" src="{{ $activity->imagen_url }}" alt="{{ $activity->titulo }}">
                            <div class="absolute bottom-4 left-4 rounded-2xl bg-red-900 px-4 py-3 text-center text-white shadow-xl">
                                @if($activity->fecha_actividad)
                                    <strong class="block text-2xl leading-none">{{ $activity->fecha_actividad->format('d') }}</strong>
                                    <small class="font-black uppercase">{{ $activity->fecha_actividad->translatedFormat('M') }}</small>
                                @else
                                    <i class="fa-regular fa-calendar text-xl"></i>
                                    <small class="ml-2 font-black uppercase">{{ __('catalog.to_confirm') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-black uppercase tracking-widest text-red-700">{{ $activity->fecha_actividad ? $activity->fecha_actividad->format('d/m/Y · H:i') : __('catalog.date_to_confirm') }}</p>
                            <h3 class="mt-2 text-xl font-black text-gray-950">{{ $activity->titulo }}</h3>
                            @if($activity->municipio)<p class="mt-3 text-sm font-black text-red-800"><i class="fa-solid fa-map-location-dot mr-2"></i>{{ $activity->municipio->nombre }}</p>@endif
                            @if($activity->lugar)<p class="mt-3 text-sm text-gray-600"><i class="fa-solid fa-location-dot mr-2 text-red-700"></i>{{ $activity->lugar }}</p>@endif
                            <p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-600">{{ $activity->descripcion }}</p>
                            <span class="mt-5 inline-flex items-center gap-2 font-black text-red-800">{{ __('catalog.view_activity') }} <i class="fa-solid fa-arrow-right transition group-hover:translate-x-1"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
