@extends('layouts.app')

@section('title', $servicioTuristico['titulo'])
@section('description', $servicioTuristico['subtitulo'])

@section('content')
<section class="relative overflow-hidden bg-gray-950 pt-32 text-white">
    <div class="absolute inset-0 opacity-80" style="background: radial-gradient(circle at 20% 20%, {{ $servicioTuristico['color'] }}66, transparent 28%), linear-gradient(135deg, #4a0711, #111827 62%, {{ $servicioTuristico['accent'] }});"></div>
    <div class="container-custom relative pb-16">
        <a class="inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-bold ring-1 ring-white/20" href="{{ route('servicios.index') }}">Todos los servicios</a>
        <h1 class="mt-6 text-4xl font-black md:text-6xl">{{ $servicioTuristico['titulo'] }}</h1>
        <p class="mt-4 max-w-3xl text-lg text-white/80">{{ $servicioTuristico['subtitulo'] }}</p>
        <p class="mt-5 text-sm font-black uppercase tracking-widest text-amber-300">{{ $providers->count() }} prestador(es) aprobado(s)</p>
    </div>
</section>

<section class="bg-[#fff7f4] py-16">
    <div class="container-custom">
        @if($providers->isEmpty())
            <div class="rounded-3xl bg-white p-10 text-center shadow-xl"><i class="fa-solid fa-hourglass-half text-4xl text-red-700"></i><h2 class="mt-4 text-2xl font-black">Todavía no hay prestadores aprobados</h2><p class="mt-2 text-gray-600">Los registros aparecerán aquí después de ser revisados y aprobados.</p><a class="mt-6 inline-flex rounded-full bg-red-800 px-5 py-3 font-black text-white" href="{{ route('prestadores.create') }}">Registrar un servicio</a></div>
        @else
            <div class="grid gap-6 lg:grid-cols-2">
                @foreach($providers as $provider)
                    @include('servicios._provider-card', ['provider' => $provider])
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
