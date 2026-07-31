@extends('layouts.app')

@section('title', 'Mi página turística')

@section('content')
<section class="bg-pattern pb-14 pt-32 text-white">
    <div class="container-custom flex flex-col justify-between gap-6 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[.2em] text-white/65">Portal del prestador</p>
            <h1 class="mt-3 font-display text-4xl font-black">{{ $provider->commercial_name }}</h1>
            <p class="mt-3 text-white/75">Completa tu ficha para que los turistas puedan encontrarte.</p>
        </div>
        <form method="POST" action="{{ route('prestador.logout') }}">@csrf<button class="btn-outline" type="submit"><i class="fa-solid fa-right-from-bracket mr-2"></i>Salir</button></form>
    </div>
</section>

<section class="bg-[#f8f3ec] py-12">
    <div class="container-custom">
      <div class="mx-auto max-w-5xl">
        @php($place = $provider->mapPlace)
        <div class="mb-7 rounded-3xl border border-amber-200 bg-amber-50 p-5 text-amber-950 shadow-sm md:flex md:items-center md:gap-5">
            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-amber-100 text-2xl text-amber-800"><i class="fa-solid fa-lightbulb"></i></span>
            <div class="mt-3 md:mt-0"><h2 class="text-lg font-black">Actualizar tu página es fácil</h2><p class="mt-1 leading-6">Completa los bloques en orden. Primero guarda y revisa la vista previa; cuando estés conforme, presiona <strong>Guardar y publicar</strong>.</p></div>
        </div>
        <div class="mb-7 grid gap-3 md:grid-cols-3">
            <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm"><span class="grid h-10 w-10 place-items-center rounded-full bg-red-950 font-black text-white">1</span><div><strong class="block">Completa</strong><small class="text-gray-500">Textos, fotos y ubicación</small></div></div>
            <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm"><span class="grid h-10 w-10 place-items-center rounded-full bg-red-950 font-black text-white">2</span><div><strong class="block">Revisa</strong><small class="text-gray-500">Mira la vista previa</small></div></div>
            <div class="flex items-center gap-3 rounded-2xl bg-white p-4 shadow-sm"><span class="grid h-10 w-10 place-items-center rounded-full bg-red-950 font-black text-white">3</span><div><strong class="block">Publica</strong><small class="text-gray-500">Haz visible tu página</small></div></div>
        </div>
        <div class="mb-7 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-white p-5 shadow-sm"><small class="font-black uppercase tracking-wider text-gray-500">Registro institucional</small><strong class="mt-2 block text-lg {{ $provider->status === 'approved' ? 'text-green-700' : 'text-amber-700' }}">{{ $provider->status === 'approved' ? 'Prestador aprobado' : 'En revisión' }}</strong></div>
            <div class="rounded-2xl bg-white p-5 shadow-sm"><small class="font-black uppercase tracking-wider text-gray-500">Publicación en el mapa</small><strong class="mt-2 block text-lg {{ $place?->activo ? 'text-green-700' : 'text-amber-700' }}">{{ $place?->activo ? 'Visible para el público' : ($place ? 'Borrador guardado' : 'Ficha sin crear') }}</strong></div>
        </div>
        @if(session('success'))<div class="mb-6 rounded-xl bg-green-50 p-4 font-bold text-green-800">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="mb-6 rounded-xl bg-red-50 p-4 text-red-800"><strong>Revisa los campos indicados.</strong><ul class="mt-2 list-inside list-disc text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

        <form id="provider-portal-form" class="provider-portal-form space-y-8 rounded-3xl bg-white p-6 shadow-xl md:p-10" method="POST" action="{{ route('prestador.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="border-b border-gray-100 pb-6"><p class="text-sm font-black uppercase tracking-[.18em] text-red-800">Información pública</p><h2 class="mt-2 text-3xl font-black text-gray-950">Tu página en el mapa</h2><p class="mt-2 max-w-2xl leading-6 text-gray-600">Escribe de forma sencilla lo que ofreces. Los turistas verán exactamente esta información.</p></div>
            <div class="grid gap-5 md:grid-cols-2">
                <label class="block md:col-span-2"><span class="provider-label">Tipo o categoría *</span><select class="provider-input" name="attraction_type_id" required><option value="">Selecciona</option>@foreach($types as $type)<option value="{{ $type->id }}" @selected(old('attraction_type_id', $place?->attraction_type_id) == $type->id)>{{ $type->parent ? $type->parent->nombre.' / ' : '' }}{{ $type->nombre }}</option>@endforeach</select></label>
                <label class="block md:col-span-2"><span class="provider-label">Descripción breve *</span><textarea class="provider-input min-h-24" name="resumen" maxlength="500" required>{{ old('resumen', $place?->resumen) }}</textarea></label>
                <label class="block md:col-span-2"><span class="provider-label">Información completa *</span><textarea class="provider-input min-h-40" name="descripcion" maxlength="5000" required>{{ old('descripcion', $place?->descripcion) }}</textarea></label>
                <label class="block md:col-span-2"><span class="provider-label">Foto principal {{ $place?->imagen ? '(deja vacío para conservarla)' : '*' }}</span><input class="provider-input" type="file" name="imagen" accept=".jpg,.jpeg,.png,.webp" @required(!$place?->imagen)>@if($place?->imagen)<img class="mt-3 h-40 w-full rounded-xl object-cover" src="{{ $place->imagen_url }}" alt="">@endif</label>
                <label class="block md:col-span-2"><span class="provider-label">Galería (máximo 5 fotos)</span><input class="provider-input" type="file" name="galeria[]" accept=".jpg,.jpeg,.png,.webp" multiple><small class="mt-2 block text-gray-500">Si seleccionas nuevas fotos, reemplazarán la galería actual. Máximo 4 MB por imagen.</small>@if($place?->galeria_urls)<span class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-5">@foreach($place->galeria_urls as $image)<img class="h-20 w-full rounded-lg object-cover" src="{{ $image }}" alt="">@endforeach</span>@endif</label>
                <label class="block"><span class="provider-label">Latitud *</span><input class="provider-input" type="number" step="0.0000001" name="latitud" value="{{ old('latitud', $place?->latitud) }}" placeholder="-21.5355000" required></label>
                <label class="block"><span class="provider-label">Longitud *</span><input class="provider-input" type="number" step="0.0000001" name="longitud" value="{{ old('longitud', $place?->longitud) }}" placeholder="-64.7296000" required></label>
                <p class="md:col-span-2 -mt-2 text-sm text-gray-500"><i class="fa-solid fa-circle-info mr-1"></i>Copia las coordenadas de Google Maps u OpenStreetMap. Deben corresponder al departamento de Tarija.</p>
                <label class="block md:col-span-2"><span class="provider-label">Dirección *</span><input class="provider-input" name="direccion" value="{{ old('direccion', $place?->direccion ?: $provider->address) }}" required></label>
                <label class="block"><span class="provider-label">Teléfono público</span><input class="provider-input" name="telefono" value="{{ old('telefono', $place?->telefono ?: $provider->whatsapp) }}"></label>
                <label class="block"><span class="provider-label">Sitio web</span><input class="provider-input" type="url" name="sitio_web" value="{{ old('sitio_web', $place?->sitio_web ?: $provider->website) }}"></label>
                <label class="block"><span class="provider-label">Horario</span><input class="provider-input" name="horario" value="{{ old('horario', $place?->horario) }}" placeholder="Lun–Dom, 08:00–22:00"></label>
                <label class="block"><span class="provider-label">Precio o referencia</span><input class="provider-input" name="precio" value="{{ old('precio', $place?->precio) }}" placeholder="Desde Bs 200"></label>

                <div class="md:col-span-2 rounded-2xl border border-blue-100 bg-blue-50/70 p-5 md:p-6">
                    <div class="flex items-start gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-blue-100 text-xl text-blue-800"><i class="fa-solid fa-share-nodes"></i></span>
                        <div><h3 class="text-lg font-black text-gray-950">Redes sociales y contacto</h3><p class="mt-1 text-sm leading-6 text-gray-600">Son opcionales. Copia el enlace completo de cada perfil para que los turistas puedan encontrarte.</p></div>
                    </div>
                    <div class="mt-5 grid gap-5 md:grid-cols-2">
                        <label class="block"><span class="provider-label"><i class="fa-brands fa-whatsapp mr-2 text-green-600"></i>WhatsApp</span><input class="provider-input" name="whatsapp" value="{{ old('whatsapp', $place?->whatsapp ?: $provider->whatsapp) }}" placeholder="Ej.: 72900001"></label>
                        <label class="block"><span class="provider-label"><i class="fa-brands fa-facebook mr-2 text-blue-700"></i>Facebook</span><input class="provider-input" type="url" name="facebook" value="{{ old('facebook', $place?->facebook ?: $provider->facebook) }}" placeholder="https://facebook.com/tu-pagina"></label>
                        <label class="block"><span class="provider-label"><i class="fa-brands fa-instagram mr-2 text-pink-600"></i>Instagram</span><input class="provider-input" type="url" name="instagram" value="{{ old('instagram', $place?->instagram ?: $provider->instagram) }}" placeholder="https://instagram.com/tu-cuenta"></label>
                        <label class="block"><span class="provider-label"><i class="fa-brands fa-tiktok mr-2 text-gray-950"></i>TikTok</span><input class="provider-input" type="url" name="tiktok" value="{{ old('tiktok', $place?->tiktok ?: $provider->tiktok) }}" placeholder="https://tiktok.com/@tu-cuenta"></label>
                        <label class="block"><span class="provider-label"><i class="fa-brands fa-x-twitter mr-2 text-gray-950"></i>X (antes Twitter)</span><input class="provider-input" type="url" name="x_url" value="{{ old('x_url', $place?->x_url ?: $provider->x_url) }}" placeholder="https://x.com/tu-cuenta"></label>
                        <label class="block"><span class="provider-label"><i class="fa-brands fa-youtube mr-2 text-red-700"></i>YouTube</span><input class="provider-input" type="url" name="youtube_url" value="{{ old('youtube_url', $place?->youtube_url ?: $provider->youtube_url) }}" placeholder="https://www.youtube.com/@tu-canal"></label>
                    </div>
                </div>

                @if($provider->provider_type === 'hospedaje')
                    <div class="md:col-span-2 rounded-2xl bg-red-50 p-5"><h3 class="font-black text-red-950"><i class="fa-solid fa-hotel mr-2"></i>Información del alojamiento</h3><div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label class="block"><span class="provider-label">Categoría o tipo</span><input class="provider-input" name="detail_1" maxlength="200" value="{{ old('detail_1', data_get($place?->service_details, 'detail_1', $provider->lodging_type)) }}" placeholder="Hotel, hostal, residencial..."></label>
                        <label class="block"><span class="provider-label">Tipos de habitación</span><select class="provider-input min-h-32" name="room_options[]" multiple>@foreach(['Individual','Matrimonial','Dos camas','Familiar','Habitaciones múltiples','Suite'] as $option)<option @selected(in_array($option, old('room_options', $place?->room_options ?? [])))>{{ $option }}</option>@endforeach</select></label>
                        <label class="block md:col-span-2"><span class="provider-label">Servicios principales</span><textarea class="provider-input min-h-24" name="detail_2" maxlength="500">{{ old('detail_2', data_get($place?->service_details, 'detail_2', implode(', ', $provider->servicesProvided()))) }}</textarea></label>
                    </div></div>
                @elseif($provider->provider_type === 'guia_departamental')
                    <div class="md:col-span-2 rounded-2xl bg-red-50 p-5"><h3 class="font-black text-red-950"><i class="fa-solid fa-id-card mr-2"></i>Perfil del guía</h3><div class="mt-4 grid gap-4 md:grid-cols-2">
                        <label class="block"><span class="provider-label">Idiomas</span><input class="provider-input" name="detail_1" maxlength="200" value="{{ old('detail_1', data_get($place?->service_details, 'detail_1', implode(', ', $provider->languages ?? []))) }}"></label>
                        <label class="block"><span class="provider-label">Especialidades</span><input class="provider-input" name="detail_2" maxlength="500" value="{{ old('detail_2', data_get($place?->service_details, 'detail_2', implode(', ', $provider->specialties ?? []))) }}"></label>
                        <label class="block md:col-span-2"><span class="provider-label">Rutas y zonas donde trabaja</span><textarea class="provider-input min-h-24" name="detail_3" maxlength="500">{{ old('detail_3', data_get($place?->service_details, 'detail_3', $provider->main_destinations)) }}</textarea></label>
                    </div></div>
                @elseif($provider->provider_type === 'gastronomia')
                    <div class="md:col-span-2 rounded-2xl bg-red-50 p-5"><h3 class="font-black text-red-950"><i class="fa-solid fa-utensils mr-2"></i>Información gastronómica</h3><div class="mt-4 grid gap-4 md:grid-cols-2"><label class="block"><span class="provider-label">Tipo de cocina</span><input class="provider-input" name="detail_1" maxlength="200" value="{{ old('detail_1', data_get($place?->service_details, 'detail_1')) }}"></label><label class="block"><span class="provider-label">Especialidades</span><input class="provider-input" name="detail_2" maxlength="500" value="{{ old('detail_2', data_get($place?->service_details, 'detail_2')) }}"></label></div></div>
                @else
                    <div class="md:col-span-2 rounded-2xl bg-red-50 p-5"><h3 class="font-black text-red-950"><i class="fa-solid fa-briefcase mr-2"></i>Información de tu servicio</h3><div class="mt-4 grid gap-4"><label class="block"><span class="provider-label">Servicios o modalidades</span><input class="provider-input" name="detail_1" maxlength="200" value="{{ old('detail_1', data_get($place?->service_details, 'detail_1', implode(', ', $provider->servicesProvided()))) }}"></label><label class="block"><span class="provider-label">Cobertura, rutas o paquetes</span><textarea class="provider-input min-h-24" name="detail_2" maxlength="500">{{ old('detail_2', data_get($place?->service_details, 'detail_2', $provider->main_destinations)) }}</textarea></label></div></div>
                @endif
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <button class="flex-1 rounded-xl border-2 border-red-900 bg-white px-6 py-4 font-black text-red-900 transition hover:bg-red-50" type="submit" name="action" value="preview"><i class="fa-solid fa-eye mr-2"></i>1. Guardar y revisar</button>
                <button class="btn-primary flex-1 py-4 text-base" type="submit" name="action" value="publish"><i class="fa-solid fa-globe mr-2"></i>2. Guardar y publicar</button>
            </div>
        </form>
        @php($contentLabels = $provider->contentLabels())
        <section class="mt-8 rounded-3xl bg-white p-6 shadow-xl md:p-9">
            <div class="flex flex-col justify-between gap-4 border-b border-gray-100 pb-6 sm:flex-row sm:items-center">
                <div><p class="text-sm font-black uppercase tracking-[.16em] text-red-800">Contenido especializado</p><h2 class="mt-2 text-2xl font-black text-gray-950">{{ $contentLabels[0] }}</h2><p class="mt-2 text-gray-600">Agrega y actualiza lo que ofreces sin modificar la información principal de tu página.</p></div>
                <a class="btn-primary shrink-0" href="{{ route('prestador.offers.create') }}"><i class="fa-solid fa-plus mr-2"></i>Agregar {{ $contentLabels[1] }}</a>
            </div>
            <div class="mt-6 grid gap-5 md:grid-cols-2">
                @forelse($provider->offerings as $offering)
                    <article class="overflow-hidden rounded-2xl border border-gray-100 bg-gray-50">
                        <img class="h-44 w-full object-cover" src="{{ $offering->imagen_url }}" alt="{{ $offering->titulo }}">
                        <div class="p-5"><div class="flex items-start justify-between gap-3"><h3 class="text-lg font-black text-gray-950">{{ $offering->titulo }}</h3><span class="rounded-full px-2.5 py-1 text-xs font-black {{ $offering->activo ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">{{ $offering->activo ? 'Visible' : 'Oculto' }}</span></div><p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-600">{{ $offering->resumen }}</p><div class="mt-4 flex gap-2"><a class="flex-1 rounded-lg bg-white px-3 py-2 text-center text-sm font-bold text-red-800 ring-1 ring-gray-200" href="{{ route('prestador.offers.edit', $offering) }}"><i class="fa-solid fa-pen mr-1"></i>Editar</a><form method="POST" action="{{ route('prestador.offers.destroy', $offering) }}" onsubmit="return confirm('¿Eliminar este contenido de tu portal?')">@csrf @method('DELETE')<button class="rounded-lg bg-red-50 px-3 py-2 text-sm font-bold text-red-700" type="submit" aria-label="Eliminar"><i class="fa-solid fa-trash"></i></button></form></div></div>
                    </article>
                @empty
                    <div class="rounded-2xl border-2 border-dashed border-gray-200 p-8 text-center md:col-span-2"><i class="fa-solid fa-images text-3xl text-gray-300"></i><h3 class="mt-3 font-black text-gray-900">Todavía no agregaste contenido</h3><p class="mt-1 text-sm text-gray-500">Comienza publicando tu primer {{ $contentLabels[1] }}.</p></div>
                @endforelse
            </div>
        </section>
        <details class="mt-7 rounded-3xl bg-white p-6 shadow-lg">
            <summary class="cursor-pointer font-black text-gray-950"><i class="fa-solid fa-key mr-2 text-red-800"></i>Cambiar contraseña de acceso</summary>
            @if(session('account_success'))<div class="mt-4 rounded-xl bg-green-50 p-4 font-bold text-green-800">{{ session('account_success') }}</div>@endif
            <form class="mt-5 grid gap-4 md:grid-cols-3" method="POST" action="{{ route('prestador.password') }}">@csrf @method('PUT')
                <label><span class="provider-label">Contraseña temporal o actual</span><input class="provider-input" type="password" name="current_password" required></label>
                <label><span class="provider-label">Nueva contraseña</span><input class="provider-input" type="password" name="password" minlength="12" required></label>
                <label><span class="provider-label">Confirmar nueva contraseña</span><input class="provider-input" type="password" name="password_confirmation" minlength="12" required></label>
                <button class="btn-primary md:col-span-3 md:w-fit" type="submit">Actualizar contraseña</button>
            </form>
        </details>
      </div>
    </div>
</section>
@endsection
