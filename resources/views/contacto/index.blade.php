@extends('layouts.app')

@section('title', 'Contacto')

@section('content')
<section class="bg-pattern pt-32 pb-16 text-white">
    <div class="container-custom">
        <p class="text-sm font-semibold uppercase tracking-wide text-coral-200">{{ __('portal.contact_eyebrow') }}</p>
        <h1 class="mt-3 font-display text-4xl font-black sm:text-5xl">{{ __('ui.contact') }}</h1>
        <p class="mt-4 max-w-2xl text-white/80">{{ __('portal.contact_intro') }}</p>
    </div>
</section>

<section class="py-16">
    <div class="container-custom grid gap-10 lg:grid-cols-[1fr_1.2fr]">
        <div class="rounded-3xl bg-white p-8 shadow-xl">
            <h2 class="text-2xl font-bold">{{ __('portal.information') }}</h2>
            <p class="mt-4 text-gray-600">{{ __('portal.information_help') }}</p>
            <div class="mt-8 space-y-5 text-gray-700">
                <p class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 text-ocean-700"></i><span>Tarija, Bolivia</span></p>
                <p class="flex gap-3"><i class="fa-solid fa-envelope mt-1 text-ocean-700"></i><a class="hover:text-ocean-700" href="mailto:turismo@tarija.gob.bo">turismo@tarija.gob.bo</a></p>
                <p class="flex gap-3"><i class="fa-solid fa-phone mt-1 text-ocean-700"></i><span>+591 000 00000</span></p>
            </div>
            <div id="footer-map" class="mt-8 h-64 overflow-hidden rounded-2xl bg-gray-200"></div>
        </div>

        <form class="rounded-3xl border bg-white p-8 shadow-xl" method="POST" action="{{ route('contacto.store') }}">
            @csrf
            <h2 class="text-2xl font-bold">{{ __('portal.tell_us') }}</h2>
            @if(session('success'))
                <div class="mt-5 rounded-lg bg-jungle-100 px-4 py-3 text-sm font-semibold text-jungle-700">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mt-5 rounded-lg bg-red-100 px-4 py-3 text-sm font-semibold text-red-800">{{ session('error') }}</div>
            @endif
            <div class="mt-6 grid gap-5">
                <input class="rounded-xl border-gray-300" name="nombre" type="text" placeholder="{{ __('portal.name') }}" value="{{ old('nombre') }}" required>
                <input class="rounded-xl border-gray-300" name="email" type="email" placeholder="{{ __('portal.email') }}" value="{{ old('email') }}" required>
                <input class="rounded-xl border-gray-300" name="telefono" type="text" placeholder="{{ __('portal.phone') }}" value="{{ old('telefono') }}">
                @php($motivoSeleccionado = old('motivo', request('motivo') === 'experiencia' ? 'experiencia' : 'consulta'))
                <select class="rounded-xl border-gray-300" name="motivo" required aria-label="Motivo del mensaje">
                    <option value="consulta" @selected($motivoSeleccionado === 'consulta')>Consulta general</option>
                    <option value="experiencia" @selected($motivoSeleccionado === 'experiencia')>Compartir una experiencia</option>
                    <option value="sugerencia" @selected($motivoSeleccionado === 'sugerencia')>Sugerencia para mejorar</option>
                    <option value="queja" @selected($motivoSeleccionado === 'queja')>Queja o inconveniente</option>
                    <option value="servicio" @selected($motivoSeleccionado === 'servicio')>Información sobre servicios turísticos</option>
                </select>
                <textarea class="min-h-40 rounded-xl border-gray-300" name="mensaje" placeholder="{{ __('portal.message_placeholder') }}" required>{{ old('mensaje') }}</textarea>
                @if($errors->any())
                    <div class="rounded-lg bg-coral-100 px-4 py-3 text-sm text-coral-700">{{ __('portal.form_error') }}</div>
                @endif
                <button class="btn-primary" type="submit">{{ __('portal.send') }}</button>
            </div>
        </form>
    </div>
</section>
@endsection
