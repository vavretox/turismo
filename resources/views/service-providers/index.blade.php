@extends('layouts.app')

@section('title', 'Consulta de prestadores turísticos')
@section('description', 'Consulta pública del estado y licencia de prestadores turísticos de Tarija.')

@section('content')
<section class="bg-pattern pb-16 pt-32 text-white">
    <div class="container-custom text-center">
        <p class="text-sm font-black uppercase tracking-[.2em] text-[#eadfd2]">Consulta pública</p>
        <h1 class="mt-3 font-display text-4xl font-black md:text-5xl">Estado de prestadores turísticos</h1>
        <p class="mx-auto mt-4 max-w-2xl text-white/80">Busque por nombre comercial para verificar el estado del trámite y la vigencia de su licencia turística departamental.</p>
    </div>
</section>

<section class="min-h-[500px] bg-[#f8f3ec] py-14">
    <div class="container-custom max-w-4xl">
        <form class="rounded-3xl bg-white p-5 shadow-xl md:p-8" method="GET" action="{{ route('prestadores.index') }}">
            <label class="provider-label text-base" for="provider-search">Nombre comercial del prestador</label>
            <div class="mt-2 flex flex-col gap-3 sm:flex-row">
                <div class="flex flex-1 items-stretch gap-2">
                    <span class="grid w-12 shrink-0 place-items-center rounded-xl border border-gray-200 bg-gray-50 text-gray-500" aria-hidden="true">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input class="min-w-0 flex-1 rounded-xl border-gray-300 px-4 py-3 focus:border-red-800 focus:ring-red-800/20" id="provider-search" name="q" type="search" minlength="2" maxlength="180" value="{{ $query }}" placeholder="Ej.: Hotel Tarija" required>
                </div>
                <button class="btn-primary justify-center px-8" type="submit">Consultar</button>
            </div>
            <p class="mt-3 text-xs text-gray-500">Ingrese al menos dos caracteres.</p>
        </form>

        @if($query !== '' && mb_strlen($query) < 2)
            <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-800">Ingrese al menos dos caracteres para realizar la búsqueda.</div>
        @elseif(mb_strlen($query) >= 2)
            <div class="mt-10">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-gray-950">Resultados para “{{ $query }}”</h2>
                    <span class="rounded-full bg-white px-4 py-2 text-sm font-bold text-gray-600 shadow-sm">{{ $providers->count() }} encontrado(s)</span>
                </div>

                <div class="grid gap-5">
                    @forelse($providers as $provider)
                        @php
                            $status = [
                                'pending' => ['Pendiente', 'bg-amber-100 text-amber-800', 'fa-clock'],
                                'reviewing' => ['En revisión', 'bg-blue-100 text-blue-800', 'fa-magnifying-glass'],
                                'approved' => ['Aprobado', 'bg-green-100 text-green-800', 'fa-circle-check'],
                                'rejected' => ['No aprobado', 'bg-red-100 text-red-800', 'fa-circle-xmark'],
                            ][$provider->status] ?? ['Pendiente', 'bg-gray-100 text-gray-700', 'fa-clock'];
                            $licenseCurrent = $provider->status === 'approved'
                                && $provider->has_tourism_license
                                && $provider->tourism_license_renewed_at
                                && $provider->tourism_license_renewed_at->endOfDay()->isFuture();
                            $providerTypes = ['hospedaje'=>'Hospedaje','agencia_viajes'=>'Agencia de viajes','operadora_turismo'=>'Operadora de turismo','guia_departamental'=>'Guía departamental','otro'=>'Otro'];
                        @endphp
                        <article class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg shadow-red-950/5">
                            <div class="grid gap-5 p-6 md:grid-cols-[1fr_auto] md:items-center md:p-8">
                                <div>
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-2xl font-black text-gray-950">{{ $provider->commercial_name }}</h3>
                                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-black {{ $status[1] }}"><i class="fa-solid {{ $status[2] }}"></i>{{ $status[0] }}</span>
                                    </div>
                                    <p class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-600">
                                        <span><i class="fa-solid fa-briefcase mr-2 text-red-800"></i>{{ $providerTypes[$provider->provider_type] ?? $provider->provider_type_other }}</span>
                                        <span><i class="fa-solid fa-location-dot mr-2 text-red-800"></i>{{ $provider->municipality }}, {{ $provider->department }}</span>
                                    </p>
                                </div>
                                <div class="w-full rounded-2xl {{ $licenseCurrent ? 'bg-green-50 text-green-800' : 'bg-gray-100 text-gray-700' }} p-5 text-center md:min-w-[220px] md:w-auto">
                                    <i class="fa-solid {{ $licenseCurrent ? 'fa-shield-check' : 'fa-shield-halved' }} text-2xl"></i>
                                    <strong class="mt-2 block">{{ $licenseCurrent ? 'Licencia vigente' : 'Sin licencia vigente' }}</strong>
                                    @if($licenseCurrent)
                                        <small class="mt-1 block">Vigente hasta {{ $provider->tourism_license_renewed_at->format('d/m/Y') }}</small>
                                    @elseif($provider->status !== 'approved')
                                        <small class="mt-1 block">El trámite todavía no está aprobado</small>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-3xl border border-dashed border-gray-300 bg-white p-10 text-center">
                            <i class="fa-solid fa-magnifying-glass text-4xl text-gray-300"></i>
                            <h3 class="mt-4 text-xl font-black text-gray-900">No encontramos ese nombre</h3>
                            <p class="mt-2 text-gray-600">Verifique la escritura del nombre comercial e intente nuevamente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
            <a class="btn-primary justify-center" href="{{ route('prestadores.create') }}"><i class="fa-solid fa-clipboard-list mr-2"></i>Registrar un prestador</a>
            <a class="rounded-xl border border-red-900 bg-white px-6 py-3 text-center font-bold text-red-900" href="{{ route('prestador.login') }}"><i class="fa-solid fa-store mr-2"></i>Administrar mi página</a>
            <a class="rounded-xl border border-gray-300 bg-white px-6 py-3 text-center font-bold text-gray-700" href="{{ route('home') }}">Volver al inicio</a>
        </div>
    </div>
</section>
@endsection
