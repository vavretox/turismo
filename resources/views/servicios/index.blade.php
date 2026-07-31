@extends('layouts.app')

@section('title', 'Servicios turísticos aprobados')
@section('description', 'Directorio de prestadores turísticos registrados y aprobados en Tarija.')

@section('content')
<section class="bg-gradient-to-br from-red-950 via-red-900 to-amber-900 pb-16 pt-32 text-white">
    <div class="container-custom text-center"><span class="text-sm font-black uppercase tracking-[.2em] text-amber-300">Directorio oficial</span><h1 class="mt-4 text-4xl font-black md:text-6xl">Servicios turísticos aprobados</h1><p class="mx-auto mt-4 max-w-3xl text-lg text-white/75">Consulta prestadores inscritos mediante el formulario y aprobados por el equipo administrador.</p></div>
</section>
<section class="bg-[#fff7f4] py-16">
    <div class="container-custom space-y-12">
        @foreach($services as $service)
            <section>
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3"><div><h2 class="text-3xl font-black">{{ $service['titulo'] }}</h2><p class="mt-1 text-gray-600">{{ $service['subtitulo'] }}</p></div><a class="font-black text-red-800" href="{{ route('servicios.show', $service['slug']) }}">Ver categoría <i class="fa-solid fa-arrow-right ml-1"></i></a></div>
                @if($service['providers']->isEmpty())
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 text-gray-500">Sin prestadores aprobados por el momento.</div>
                @else
                    <div class="grid gap-5 lg:grid-cols-2">@foreach($service['providers']->take(4) as $provider) @include('servicios._provider-card', ['provider' => $provider]) @endforeach</div>
                @endif
            </section>
        @endforeach
    </div>
</section>
@endsection
