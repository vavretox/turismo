@extends('layouts.app')

@section('title', __('ui.municipalities'))
@section('description', 'Conoce los once municipios de Tarija, su cultura y principales atractivos turísticos.')

@section('content')
<section class="relative overflow-hidden bg-gray-950 pb-20 pt-36 text-white">
    <div class="absolute inset-0 bg-pattern opacity-80"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-gray-950/10 to-gray-950/75"></div>
    <div class="container-custom relative text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-black uppercase tracking-[.18em] backdrop-blur"><i class="fa-solid fa-mountain-sun text-[#eadfd2]"></i> {{ __('catalog.department') }}</span>
        <h1 class="mt-5 font-display text-4xl font-black sm:text-5xl md:text-6xl">{{ __('catalog.municipalities_title') }}</h1>
        <p class="mx-auto mt-5 max-w-2xl text-base leading-7 text-white/80 sm:text-lg">{{ __('catalog.municipalities_intro') }}</p>
    </div>
</section>

<section class="bg-[#fffaf7] py-16 sm:py-20">
    <div class="container-custom">
        @if($municipios->isNotEmpty())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($municipios as $municipio)
                    <a class="group overflow-hidden rounded-3xl bg-white shadow-lg ring-1 ring-red-100 transition duration-500 hover:-translate-y-2 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-red-200" href="{{ route('municipios.show', $municipio) }}" aria-label="Conocer el municipio {{ $municipio->nombre }}">
                        <div class="relative h-64 overflow-hidden">
                            <img class="h-full w-full object-cover transition duration-700 group-hover:scale-110" src="{{ $municipio->imagen_url }}" alt="Paisaje de {{ $municipio->nombre }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-950/90 via-gray-950/20 to-transparent"></div>
                            <div class="absolute inset-x-0 bottom-0 p-6 text-white">
                                <small class="font-black uppercase tracking-[.16em] text-red-100">{{ $municipio->provincia }}</small>
                                <h2 class="mt-2 text-2xl font-black sm:text-3xl">{{ $municipio->nombre }}</h2>
                            </div>
                        </div>
                        <div class="p-6">
                            <p class="line-clamp-3 text-sm leading-6 text-gray-600">{{ $municipio->resumen ?: $municipio->subtitulo }}</p>
                            <span class="mt-6 inline-flex min-h-11 items-center gap-2 font-black text-red-800">{{ __('catalog.discover_municipality') }} <i class="fa-solid fa-arrow-right transition group-hover:translate-x-2"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-3xl bg-white p-10 text-center shadow-lg">
                <i class="fa-solid fa-mountain-sun text-4xl text-red-700"></i>
                <h2 class="mt-4 text-2xl font-black">{{ __('catalog.coming_soon') }}</h2>
                <p class="mt-2 text-gray-600">{{ __('catalog.municipalities_pending') }}</p>
            </div>
        @endif
    </div>
</section>
@endsection
