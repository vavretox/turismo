<article class="rounded-3xl border border-gray-100 bg-white p-6 shadow-lg shadow-red-950/5">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-red-800">
                <i class="fa-solid fa-circle-check mr-1.5"></i>Prestador aprobado
            </span>
            <h3 class="mt-3 text-2xl font-black text-gray-950">{{ $provider->commercial_name }}</h3>
            @if($provider->business_name)<p class="mt-1 text-sm text-gray-500">{{ $provider->business_name }}</p>@endif
        </div>
        <span class="rounded-xl bg-red-50 px-3 py-2 text-sm font-bold text-red-900"><i class="fa-solid fa-location-dot mr-1"></i>{{ $provider->municipality }}</span>
    </div>

    <p class="mt-5 text-sm leading-6 text-gray-600"><strong>Dirección:</strong> {{ $provider->address }}</p>
    @if($provider->provider_type === 'hospedaje')
        <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold text-red-800">
            @if($provider->lodging_type)<span class="rounded-full bg-red-50 px-3 py-1.5">{{ ucfirst(str_replace('_', ' ', $provider->lodging_type)) }}</span>@endif
            @if($provider->room_count)<span class="rounded-full bg-red-50 px-3 py-1.5">{{ $provider->room_count }} habitaciones</span>@endif
            @if($provider->guest_capacity)<span class="rounded-full bg-red-50 px-3 py-1.5">Capacidad: {{ $provider->guest_capacity }}</span>@endif
        </div>
    @endif
    @if($provider->provider_type === 'guia_departamental')
        <div class="mt-3 text-sm text-gray-600">
            @if($provider->experience_years !== null)<p><strong>Experiencia:</strong> {{ $provider->experience_years }} años</p>@endif
            @if(count($provider->languages ?? []))<p><strong>Idiomas:</strong> {{ implode(', ', $provider->languages) }}</p>@endif
        </div>
    @endif

    <div class="mt-5 flex flex-wrap gap-2">
        @foreach($provider->servicesProvided() as $service)
            <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-bold text-gray-700">{{ $service }}</span>
        @endforeach
    </div>

    <div class="mt-6 flex flex-wrap gap-2 border-t border-gray-100 pt-5">
        <a class="rounded-full bg-red-800 px-4 py-2 text-sm font-black text-white hover:bg-red-700" href="https://wa.me/{{ preg_replace('/\D+/', '', $provider->whatsapp) }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp mr-1"></i>WhatsApp</a>
        <a class="rounded-full bg-gray-100 px-4 py-2 text-sm font-bold text-gray-800" href="mailto:{{ $provider->email }}"><i class="fa-solid fa-envelope mr-1"></i>Correo</a>
        @if($provider->mapPlace?->activo)
            <a class="rounded-full bg-gray-100 px-4 py-2 text-sm font-bold text-gray-800" href="{{ route('lugares.show', $provider->mapPlace) }}"><i class="fa-solid fa-window-maximize mr-1"></i>Portal web</a>
            <a class="rounded-full bg-gray-100 px-4 py-2 text-sm font-bold text-gray-800" href="{{ route('mapa.interactivo', ['prestador' => $provider->id]) }}"><i class="fa-solid fa-map-location-dot mr-1"></i>Ver en el mapa</a>
        @endif
    </div>
</article>
