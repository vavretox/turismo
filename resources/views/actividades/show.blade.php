@extends('layouts.app')

@section('title', $actividad->titulo)
@section('description', $actividad->descripcion)

@section('content')
<section class="relative h-screen min-h-[640px] overflow-hidden bg-red-950 pt-20 text-white supports-[height:100svh]:h-[100svh]">
    @if($actividad->video_url)
        <video
            id="activity-hero-video"
            class="absolute inset-0 h-full w-full object-cover opacity-45 transition-opacity duration-700"
            src="{{ $actividad->video_url }}"
            poster="{{ $actividad->imagen_url }}"
            muted
            loop
            playsinline
            preload="metadata"
        ></video>
    @else
        <img class="absolute inset-0 h-full w-full object-cover opacity-35" src="{{ $actividad->imagen_url }}" alt="{{ $actividad->titulo }}">
    @endif
    <div
        @if($actividad->video_url) id="activity-hero-overlay" @endif
        class="pointer-events-none absolute inset-0 bg-gradient-to-r from-red-950 via-red-950/90 to-red-950/35 transition-opacity duration-700"
    ></div>
    <div class="container-custom relative flex h-full items-center py-10 md:py-14">
        <div
            @if($actividad->video_url) id="activity-hero-information" @endif
            class="{{ $actividad->video_url ? 'w-fit max-w-4xl rounded-3xl bg-black/45 p-6 shadow-2xl backdrop-blur-[2px] transition duration-500 md:p-8' : '' }}"
        >
        <span class="inline-flex items-center gap-2 rounded-full bg-amber-400 px-4 py-2 text-xs font-black uppercase tracking-wider text-red-950"><i class="fa-solid fa-calendar-star"></i> Actividad de la semana</span>
        @if($actividad->subtitulo)<p class="mt-6 text-sm font-black uppercase tracking-[.2em] text-amber-300">{{ $actividad->subtitulo }}</p>@endif
        <h1 class="mt-3 max-w-4xl text-4xl font-black leading-tight md:text-6xl">{{ $actividad->titulo }}</h1>
        <p class="mt-6 max-w-2xl text-lg leading-8 text-white/90">{{ $actividad->descripcion }}</p>
        </div>
    </div>
    @if($actividad->video_url)
        <div class="activity-video-controls absolute right-4 top-24 z-20 flex max-w-[calc(100%-2rem)] flex-wrap justify-end gap-3 sm:right-7 sm:top-28">
            <button id="activity-video-toggle" type="button" class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-black text-red-950 shadow-xl transition hover:-translate-y-0.5 hover:bg-amber-300">
                <i class="fa-solid fa-play"></i>
                <span>Reproducir video</span>
            </button>
            <button id="activity-video-sound" type="button" class="inline-flex items-center gap-2 rounded-full bg-black/65 px-5 py-3 text-sm font-black text-white ring-1 ring-white/30 backdrop-blur transition hover:bg-black/80">
                <i class="fa-solid fa-volume-xmark"></i>
                <span>Activar sonido</span>
            </button>
        </div>
    @endif
</section>

<section class="py-14 md:py-20">
    <div class="container-custom grid min-w-0 gap-10 lg:grid-cols-[minmax(0,1fr)_320px]">
        <article class="min-w-0 space-y-10">
            <div class="overflow-hidden rounded-[28px] bg-white shadow-xl shadow-red-950/10">
                <div class="p-7 md:p-10">
                    <h2 class="text-2xl font-black text-gray-950">Información de la actividad</h2>
                    <div class="mt-5 whitespace-pre-line text-base leading-8 text-gray-600">{{ $actividad->contenido ?: $actividad->descripcion }}</div>
                </div>
            </div>

            @if(collect($actividad->sectores_interes)->isNotEmpty())
                <div class="min-w-0 max-w-2xl">
                    <h2 class="text-3xl font-black text-gray-950">Sectores y puntos de interés</h2>
                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        @foreach($actividad->sectores_interes as $sector)
                            <article class="rounded-3xl bg-white p-6 shadow-lg shadow-red-950/10 ring-1 ring-red-100">
                                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-red-100 text-xl text-red-800"><i class="fa-solid {{ $sector['icono'] ?? 'fa-location-dot' }}"></i></div>
                                <h3 class="mt-5 text-xl font-black text-gray-950">{{ $sector['titulo'] ?? '' }}</h3>
                                <p class="mt-3 leading-7 text-gray-600">{{ $sector['descripcion'] ?? '' }}</p>
                                @if(filled($sector['enlace'] ?? null))<a class="mt-4 inline-flex items-center gap-2 text-sm font-black text-red-800" href="{{ $sector['enlace'] }}">Más información <i class="fa-solid fa-arrow-right"></i></a>@endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($actividad->galeria_urls->isNotEmpty())
                <div>
                    <h2 class="text-3xl font-black text-gray-950">Galería de fotos</h2>
                    <p class="mt-2 text-sm text-gray-500">Pulsa una fotografía para verla a pantalla completa.</p>
                    <div class="activity-film mt-6">
                        <div class="activity-film-track">
                            @foreach($actividad->galeria_urls as $foto)
                                <button type="button" class="activity-film-frame" data-activity-lightbox="{{ $foto }}" aria-label="Ampliar fotografía">
                                    <img src="{{ $foto }}" alt="Fotografía de {{ $actividad->titulo }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if($actividad->mapa_url)
                <div class="overflow-hidden rounded-[28px] bg-white shadow-xl shadow-red-950/10">
                    <div class="p-7 md:p-9"><h2 class="text-3xl font-black text-gray-950">Cómo llegar</h2>@if($actividad->direccion)<p class="mt-3 text-gray-600">{{ $actividad->direccion }}</p>@endif</div>
                    @if(str_contains($actividad->mapa_url, '/embed'))
                        <iframe class="h-[300px] w-full border-0 sm:h-[360px] lg:h-[420px]" src="{{ $actividad->mapa_url }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mapa de {{ $actividad->titulo }}"></iframe>
                    @else
                        <div class="px-7 pb-8 md:px-9"><a href="{{ $actividad->mapa_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-2xl bg-green-700 px-5 py-3 text-sm font-black text-white"><i class="fa-solid fa-map-location-dot"></i> Abrir ubicación en Google Maps</a></div>
                    @endif
                </div>
            @endif
        </article>

        <aside>
            <div class="sticky top-28 rounded-[28px] bg-white p-7 shadow-xl shadow-red-950/10 ring-1 ring-red-100">
                <h2 class="text-xl font-black text-gray-950">Datos de la actividad</h2>
                <div class="mt-6 space-y-5 text-sm text-gray-600">
                    @if($actividad->fecha_actividad)<p class="flex gap-3"><i class="fa-solid fa-calendar-day mt-1 w-5 text-red-800"></i><span><strong class="block text-gray-950">Fecha y hora</strong>{{ $actividad->fecha_actividad->format('d/m/Y · H:i') }}</span></p>@endif
                    @if($actividad->municipio)<p class="flex gap-3"><i class="fa-solid fa-map-location-dot mt-1 w-5 text-red-800"></i><span><strong class="block text-gray-950">Municipio</strong>{{ $actividad->municipio->nombre }}</span></p>@endif
                    @if($actividad->lugar)<p class="flex gap-3"><i class="fa-solid fa-location-dot mt-1 w-5 text-red-800"></i><span><strong class="block text-gray-950">Lugar</strong>{{ $actividad->lugar }}</span></p>@endif
                    @if($actividad->horarios)<p class="flex gap-3"><i class="fa-solid fa-clock mt-1 w-5 text-red-800"></i><span><strong class="block text-gray-950">Horarios</strong><span class="whitespace-pre-line">{{ $actividad->horarios }}</span></span></p>@endif
                    @if($actividad->telefono)<p class="flex gap-3"><i class="fa-solid fa-phone mt-1 w-5 text-red-800"></i><span><strong class="block text-gray-950">Teléfono</strong><a href="tel:{{ $actividad->telefono }}">{{ $actividad->telefono }}</a></span></p>@endif
                    @if($actividad->whatsapp)<a class="flex gap-3" href="https://wa.me/{{ preg_replace('/\D+/', '', $actividad->whatsapp) }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp mt-1 w-5 text-green-600"></i><span><strong class="block text-gray-950">WhatsApp</strong>{{ $actividad->whatsapp }}</span></a>@endif
                    @if($actividad->correo)<p class="flex gap-3"><i class="fa-solid fa-envelope mt-1 w-5 text-red-800"></i><span><strong class="block text-gray-950">Correo</strong><a class="break-all" href="mailto:{{ $actividad->correo }}">{{ $actividad->correo }}</a></span></p>@endif
                </div>
                @if($actividad->sitio_web || $actividad->facebook || $actividad->instagram || $actividad->x_url || $actividad->youtube_url)
                    <div class="mt-7 flex flex-wrap gap-2 border-t border-gray-100 pt-6">
                        @if($actividad->sitio_web)<a href="{{ $actividad->sitio_web }}" target="_blank" rel="noopener" class="grid h-11 w-11 place-items-center rounded-full bg-gray-100 text-gray-700"><i class="fa-solid fa-globe"></i></a>@endif
                        @if($actividad->facebook)<a href="{{ $actividad->facebook }}" target="_blank" rel="noopener" class="grid h-11 w-11 place-items-center rounded-full bg-blue-100 text-blue-700"><i class="fa-brands fa-facebook-f"></i></a>@endif
                        @if($actividad->instagram)<a href="{{ $actividad->instagram }}" target="_blank" rel="noopener" class="grid h-11 w-11 place-items-center rounded-full bg-pink-100 text-pink-700"><i class="fa-brands fa-instagram"></i></a>@endif
                        @if($actividad->x_url)<a href="{{ $actividad->x_url }}" target="_blank" rel="noopener" aria-label="X" class="grid h-11 w-11 place-items-center rounded-full bg-gray-900 text-white"><i class="fa-brands fa-x-twitter"></i></a>@endif
                        @if($actividad->youtube_url)<a href="{{ $actividad->youtube_url }}" target="_blank" rel="noopener" aria-label="YouTube" class="grid h-11 w-11 place-items-center rounded-full bg-red-100 text-red-700"><i class="fa-brands fa-youtube"></i></a>@endif
                    </div>
                @endif
                <div class="mt-7 border-t border-gray-100 pt-6">
                    <p class="mb-3 text-sm font-black text-gray-950">Foto principal</p>
                    <button type="button" class="group block w-full overflow-hidden rounded-2xl bg-gray-100" data-activity-lightbox="{{ $actividad->imagen_url }}">
                        <img class="h-40 w-full object-contain transition duration-300 group-hover:scale-105" src="{{ $actividad->imagen_url }}" alt="{{ $actividad->titulo }}">
                    </button>
                    <p class="mt-2 text-xs text-gray-500">Pulsa para verla completa.</p>
                </div>
                <a href="{{ route('actividades.index') }}" class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-red-900 px-5 py-3.5 text-sm font-black text-white transition hover:bg-red-700">Ver todas las actividades <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </aside>
    </div>

    @if($otrasActividades->isNotEmpty())
        <div class="container-custom mt-16">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-black uppercase tracking-wider text-red-700">Esta semana</p>
                    <h2 class="mt-2 text-3xl font-black text-gray-950">Otras actividades destacadas</h2>
                </div>
                <a href="{{ route('actividades.index') }}" class="inline-flex items-center gap-2 text-sm font-black text-red-800">Ver todas las actividades <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="mt-7 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($otrasActividades as $otra)
                    <a href="{{ route('actividades.show', $otra) }}" class="group overflow-hidden rounded-3xl bg-white shadow-lg shadow-red-950/10">
                        <img class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" src="{{ $otra->imagen_url }}" alt="{{ $otra->titulo }}">
                        <div class="p-5">
                            <h3 class="text-xl font-black text-gray-950">{{ $otra->titulo }}</h3>
                            @if($otra->fecha_actividad)<p class="mt-2 text-sm font-bold text-red-700">{{ $otra->fecha_actividad->format('d/m/Y · H:i') }}</p>@endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>

<div id="activity-lightbox" class="activity-lightbox" hidden>
    <button type="button" class="activity-lightbox-close" aria-label="Cerrar imagen"><i class="fa-solid fa-xmark"></i></button>
    <img src="" alt="Imagen ampliada de la actividad">
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const heroVideo = document.getElementById('activity-hero-video');
    const videoToggle = document.getElementById('activity-video-toggle');
    const soundToggle = document.getElementById('activity-video-sound');

    if (heroVideo && videoToggle) {
        const heroOverlay = document.getElementById('activity-hero-overlay');
        const heroInformation = document.getElementById('activity-hero-information');
        const playIcon = videoToggle.querySelector('i');
        const playLabel = videoToggle.querySelector('span');
        const updatePlaybackButton = function () {
            const playing = !heroVideo.paused;
            playIcon.className = playing ? 'fa-solid fa-pause' : 'fa-solid fa-play';
            playLabel.textContent = playing ? 'Pausar video' : 'Reproducir video';
            heroOverlay?.classList.toggle('opacity-0', playing);
            heroVideo.classList.toggle('opacity-45', !playing);
            heroVideo.classList.toggle('opacity-100', playing);
            heroInformation?.classList.toggle('pointer-events-none', playing);
            heroInformation?.classList.toggle('translate-y-4', playing);
            heroInformation?.classList.toggle('opacity-0', playing);
        };
        videoToggle.addEventListener('click', function () {
            if (heroVideo.paused) {
                heroVideo.play().catch(function () {});
            } else {
                heroVideo.pause();
            }
        });
        heroVideo.addEventListener('play', updatePlaybackButton);
        heroVideo.addEventListener('pause', updatePlaybackButton);
        updatePlaybackButton();
    }

    if (heroVideo && soundToggle) {
        const soundIcon = soundToggle.querySelector('i');
        const soundLabel = soundToggle.querySelector('span');
        soundToggle.addEventListener('click', function () {
            heroVideo.muted = !heroVideo.muted;
            soundIcon.className = heroVideo.muted ? 'fa-solid fa-volume-xmark' : 'fa-solid fa-volume-high';
            soundLabel.textContent = heroVideo.muted ? 'Activar sonido' : 'Silenciar';
        });
    }

    const lightbox = document.getElementById('activity-lightbox');
    if (!lightbox) return;
    const image = lightbox.querySelector('img');
    const close = function () {
        lightbox.classList.remove('is-visible');
        setTimeout(function () { lightbox.hidden = true; image.src = ''; }, 180);
        document.body.style.overflow = '';
    };
    document.querySelectorAll('[data-activity-lightbox]').forEach(function (button) {
        button.addEventListener('click', function () {
            image.src = button.dataset.activityLightbox;
            lightbox.hidden = false;
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(function () { lightbox.classList.add('is-visible'); });
        });
    });
    lightbox.querySelector('.activity-lightbox-close').addEventListener('click', close);
    lightbox.addEventListener('click', function (event) { if (event.target === lightbox) close(); });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !lightbox.hidden) close(); });
});
</script>
@endpush
