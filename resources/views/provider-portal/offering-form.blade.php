@extends('layouts.app')

@php($labels = $provider->contentLabels())
@section('title', ($offering ? 'Editar ' : 'Nuevo ').$labels[1])

@section('content')
<section class="bg-pattern pb-12 pt-32 text-white"><div class="container-custom"><p class="text-sm font-black uppercase tracking-[.2em] text-white/65">{{ $labels[0] }}</p><h1 class="mt-3 text-4xl font-black">{{ $offering ? 'Editar' : 'Agregar' }} {{ $labels[1] }}</h1><p class="mt-3 text-white/75">Completa únicamente información real, clara y útil para el turista.</p></div></section>
<section class="bg-[#f8f3ec] py-12"><div class="container-custom"><form class="provider-portal-form mx-auto max-w-4xl space-y-7 rounded-3xl bg-white p-6 shadow-xl md:p-10" method="POST" action="{{ $offering ? route('prestador.offers.update', $offering) : route('prestador.offers.store') }}" enctype="multipart/form-data">@csrf @if($offering)@method('PUT')@endif
    @if($errors->any())<div class="rounded-xl bg-red-50 p-4 text-red-800"><strong>Revisa los datos.</strong><ul class="mt-2 list-inside list-disc text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="grid gap-5 md:grid-cols-2">
        <label class="md:col-span-2"><span class="provider-label">Título *</span><input class="provider-input" name="titulo" maxlength="120" value="{{ old('titulo', $offering?->titulo) }}" placeholder="{{ $labels[2] }}" required></label>
        <label class="md:col-span-2"><span class="provider-label">Descripción breve *</span><textarea class="provider-input min-h-24" name="resumen" maxlength="350" required>{{ old('resumen', $offering?->resumen) }}</textarea></label>
        <label class="md:col-span-2"><span class="provider-label">Información completa</span><textarea class="provider-input min-h-40" name="descripcion" maxlength="3000">{{ old('descripcion', $offering?->descripcion) }}</textarea></label>
        <label><span class="provider-label">{{ $labels[3] }}</span><input class="provider-input" name="duracion" maxlength="100" value="{{ old('duracion', $offering?->duracion) }}"></label>
        <label><span class="provider-label">Precio o referencia</span><input class="provider-input" name="precio" maxlength="100" value="{{ old('precio', $offering?->precio) }}" placeholder="Ej.: Desde Bs 150"></label>
        <label class="md:col-span-2"><span class="provider-label">{{ $labels[4] }}</span><textarea class="provider-input min-h-28" name="incluye" maxlength="1000">{{ old('incluye', $offering?->incluye) }}</textarea></label>
        @if(in_array($provider->provider_type, ['agencia_viajes', 'operadora_turismo'], true))
            <fieldset class="md:col-span-2 rounded-2xl border border-red-100 bg-red-50 p-5">
                <legend class="px-2 font-black text-red-950">Destinos incluidos en este paquete *</legend>
                <p class="mb-4 text-sm text-gray-600">Selecciona todos los municipios donde se realiza el paquete. Esto permite recomendarlo únicamente a viajeros interesados en esos destinos.</p>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($destinations as $destination)
                        <label class="flex items-start gap-3 rounded-xl bg-white p-3 ring-1 ring-red-100">
                            <input class="mt-1 h-5 w-5 rounded text-red-800" type="checkbox" name="destination_ids[]" value="{{ $destination->id }}" @checked(in_array($destination->id, old('destination_ids', $offering?->destination_ids ?? [])))>
                            <span><strong class="block text-gray-950">{{ $destination->nombre }}</strong><small class="text-gray-500">{{ $destination->provincia }}</small></span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endif
        <label class="md:col-span-2"><span class="provider-label">Foto principal {{ $offering?->imagen ? '(opcional al editar)' : '*' }}</span><input class="provider-input" type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp" @required(!$offering?->imagen)>@if($offering?->imagen)<img class="mt-3 h-48 w-full rounded-xl object-cover" src="{{ $offering->imagen_url }}" alt="">@endif</label>
        <label class="md:col-span-2"><span class="provider-label">Galería adicional (máximo 4 fotos)</span><input class="provider-input" type="file" name="galeria[]" accept=".jpg,.jpeg,.png,.webp" multiple></label>
        <label><span class="provider-label">Orden de aparición</span><input class="provider-input" type="number" min="0" max="1000" name="orden" value="{{ old('orden', $offering?->orden ?? 0) }}"></label>
        <label class="flex items-center gap-3 self-end rounded-xl bg-green-50 p-4 font-bold text-green-900"><input class="h-5 w-5 rounded text-green-700" type="checkbox" name="activo" value="1" @checked(old('activo', $offering?->activo ?? true))> Mostrar en mi portal web</label>
    </div>
    <div class="flex flex-col gap-3 sm:flex-row"><a class="rounded-xl border border-gray-300 px-6 py-3 text-center font-bold text-gray-700" href="{{ route('prestador.panel') }}">Cancelar</a><button class="btn-primary flex-1" type="submit"><i class="fa-solid fa-floppy-disk mr-2"></i>{{ $offering ? 'Guardar cambios' : 'Publicar contenido' }}</button></div>
</form></div></section>
@endsection
