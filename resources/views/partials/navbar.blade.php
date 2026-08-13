@php
    $navServicios = \App\Http\Controllers\ServicioTuristicoController::servicios();
    $brandName = $siteIdentity?->nombre ?: 'Secretaria Departamental de Turismo - GADT';
    $hasVisualHeader = request()->routeIs('home', 'municipios.show', 'destinos.show', 'actividades.show');
    $initialNavClass = $hasVisualHeader ? 'nav-transparent' : 'nav-solid';
    $activeNavClass = 'is-active';
@endphp

<nav class="site-navbar" x-data="{ open: false, servicesPinned: false, mobileServicesOpen: false, mobileExploreOpen: false, mobileAgendaOpen: false, scrolled: false }" x-init="scrolled = window.scrollY > 40; window.addEventListener('scroll', () => scrolled = window.scrollY > 40, { passive: true })" @keydown.escape.window="open = false" @resize.window="if (window.innerWidth >= 1280) open = false" :class="scrolled ? 'nav-solid' : '{{ $initialNavClass }}'">
    <div id="services-menu-host" class="services-menu-host relative mx-auto w-full max-w-[1800px] px-4 sm:px-6 lg:px-8">
        <div class="grid h-20 grid-cols-[minmax(0,1fr)_auto] items-center gap-3 xl:grid-cols-[minmax(180px,1fr)_auto_minmax(350px,1fr)] xl:gap-5">
            <a href="{{ route('home') }}" class="group flex min-w-0 items-center gap-3">
                <span class="grid h-12 w-12 place-items-center text-ocean-700 transition group-hover:scale-105">
                    @if($siteIdentity?->logo_url)
                        <img class="h-12 w-12 object-contain drop-shadow-md" src="{{ $siteIdentity->logo_url }}" alt="{{ $brandName }}">
                    @else
                        <i class="fa-solid fa-landmark text-2xl drop-shadow-sm"></i>
                    @endif
                </span>
                <span class="brand-text max-w-[15rem] truncate font-display text-lg font-bold leading-tight text-gradient xl:max-w-[24rem] xl:text-xl">{{ $brandName }}</span>
            </a>
            <div class="menu-panel hidden items-center justify-center gap-1 xl:flex">
                <a class="nav-link {{ request()->routeIs('home') ? $activeNavClass : '' }}" href="{{ route('home') }}"><i class="fa-solid fa-house"></i>{{ __('ui.home') }}</a>
                <div class="nav-dropdown">
                    <button class="nav-link {{ request()->routeIs('destinos*', 'municipios.*', 'mapa.*', 'inspirame', 'tours-360.*') ? $activeNavClass : '' }}" type="button" aria-haspopup="true"><i class="fa-solid fa-compass"></i>{{ __('ui.explore') }}<i class="fa-solid fa-chevron-down nav-chevron"></i></button>
                    <div class="nav-submenu nav-submenu-wide">
                        <div class="nav-submenu-heading"><span>{{ __('nav.discover_tarija') }}</span><small>{{ __('nav.choose_start') }}</small></div>
                        <div class="grid grid-cols-2 gap-2 p-3">
                            <a href="{{ route('destinos') }}"><i class="fa-solid fa-location-dot"></i><span><strong>{{ __('ui.destinations') }}</strong><small>{{ __('nav.featured_places') }}</small></span></a>
                            <a href="{{ route('mapa.interactivo') }}"><i class="fa-solid fa-map"></i><span><strong>{{ __('nav.tourism_map') }}</strong><small>{{ __('nav.explore_location') }}</small></span></a>
                            <a href="{{ route('municipios.index') }}"><i class="fa-solid fa-mountain-sun"></i><span><strong>{{ __('ui.municipalities') }}</strong><small>{{ __('nav.eleven_destinations') }}</small></span></a>
                            <a href="{{ route('inspirame') }}"><i class="fa-solid fa-wand-magic-sparkles"></i><span><strong>{{ __('ui.inspire') }}</strong><small>{{ __('nav.create_itinerary') }}</small></span></a>
                            <a href="{{ route('tours-360.index') }}" target="_blank" rel="noopener"><i class="fa-solid fa-vr-cardboard"></i><span><strong>Tours 360°</strong><small>Recorridos virtuales</small></span></a>
                        </div>
                    </div>
                </div>
                <div class="nav-dropdown">
                    <button class="nav-link {{ request()->routeIs('eventos*', 'noticias*', 'actividades*') ? $activeNavClass : '' }}" type="button" aria-haspopup="true"><i class="fa-solid fa-calendar-days"></i>{{ __('Agenda') }}<i class="fa-solid fa-chevron-down nav-chevron"></i></button>
                    <div class="nav-submenu">
                        <div class="nav-submenu-heading"><span>{{ __('nav.tarija_today') }}</span><small>{{ __('nav.activities_news') }}</small></div>
                        <div class="grid gap-2 p-3">
                            <a href="{{ route('actividades.index') }}"><i class="fa-solid fa-calendar-week"></i><span><strong>{{ __('nav.activities') }}</strong><small>{{ __('nav.upcoming_weekly') }}</small></span></a>
                            <a href="{{ route('eventos') }}"><i class="fa-solid fa-calendar-check"></i><span><strong>{{ __('nav.upcoming_events') }}</strong><small>{{ __('nav.tourism_agenda') }}</small></span></a>
                            <a href="{{ route('noticias') }}"><i class="fa-solid fa-newspaper"></i><span><strong>{{ __('ui.news') }}</strong><small>{{ __('nav.stories_news') }}</small></span></a>
                        </div>
                    </div>
                </div>
                <a class="nav-link {{ request()->routeIs('contacto') ? $activeNavClass : '' }}" href="{{ route('contacto') }}"><i class="fa-solid fa-paper-plane"></i>{{ __('ui.contact') }}</a>
            </div>
            <div class="flex items-center justify-end gap-2 xl:min-w-[350px] xl:gap-3">
                <div class="locale-switch notranslate hidden items-center gap-1 rounded-full border border-ocean-100 bg-white/80 p-1 text-sm font-bold text-gray-700 shadow-sm xl:flex" translate="no">
                    <a class="rounded-full px-3 py-1 {{ app()->getLocale() === 'es' ? 'bg-ocean-700 text-white' : 'hover:bg-ocean-50' }}" href="{{ route('idioma', 'es') }}">ES</a>
                    <a class="rounded-full px-3 py-1 {{ app()->getLocale() === 'en' ? 'bg-ocean-700 text-white' : 'hover:bg-ocean-50' }}" href="{{ route('idioma', 'en') }}">EN</a>
                </div>
                <a
                    class="hidden items-center gap-2 rounded-full border border-[#dbcdbd] bg-gradient-to-r from-[#f2eadf] to-[#ded0c0] px-4 py-2.5 text-sm font-black text-[#6f1d2c] shadow-lg shadow-stone-900/10 transition hover:-translate-y-0.5 hover:from-white hover:to-[#eadfd2] xl:inline-flex"
                    href="{{ route('inspirame') }}"
                    aria-label="Crear un itinerario en Inspírame"
                >
                    <i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
                    <span>{{ __('ui.inspire') }}</span>
                </a>
                <button
                    id="services-menu-button"
                    class="services-menu-button hidden items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition duration-300 xl:inline-flex"
                    type="button"
                    aria-expanded="false"
                    aria-controls="services-dropdown"
                >
                    <i class="fa-solid fa-compass" aria-hidden="true"></i>
                    <span>{{ __('Servicios') }}</span>
                    <i id="services-menu-chevron" class="fa-solid fa-chevron-down text-[10px] transition-transform" aria-hidden="true"></i>
                </button>
                <button class="mobile-menu-button grid h-11 w-11 place-items-center rounded-xl xl:hidden" type="button" @click="open = ! open" :aria-expanded="open" aria-controls="mobile-navigation" aria-label="Abrir menú de navegación">
                    <i class="fa-solid" :class="open ? 'fa-xmark' : 'fa-bars'"></i>
                </button>
            </div>
        </div>
        <div
            id="services-dropdown"
            class="services-dropdown absolute right-0 top-full w-[min(760px,calc(100vw-2rem))] overflow-hidden rounded-3xl border border-white/25 bg-gray-950/55 p-3 text-white shadow-2xl shadow-black/30 backdrop-blur-xl"
            aria-hidden="true"
        >
            <div class="flex items-center justify-between px-4 pb-3 pt-2">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-white">{{ __('Explora y planifica') }}</p>
                    <h2 class="mt-1 font-display text-xl font-black">{{ __('Servicios turísticos') }}</h2>
                </div>
                <a class="text-sm font-bold text-white transition hover:text-white/75" href="{{ route('servicios.index') }}" @click="servicesPinned = false">{{ __('Ver todos') }}</a>
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach($navServicios as $slug => $servicio)
                    <a class="service-dropdown-link group flex items-center gap-3 rounded-2xl p-3" href="{{ route('servicios.show', $slug) }}">
                        <span class="service-dropdown-icon" style="--service-color: {{ $servicio['color'] }};"><i class="fa-solid {{ $servicio['icono'] }}"></i></span>
                        <span class="min-w-0">
                            <span class="block font-black text-white">{{ $servicio['titulo'] }}</span>
                            <span class="mt-0.5 block truncate text-xs text-white">{{ $servicio['subtitulo'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
        <div id="mobile-navigation" class="max-h-[calc(100dvh-6rem)] space-y-2 overflow-y-auto overscroll-contain rounded-2xl bg-white/95 p-4 pb-4 shadow-xl ring-1 ring-ocean-100 xl:hidden" x-cloak x-show="open" x-transition @click.outside="open = false" @click="if ($event.target.closest('a')) open = false">
            <a class="block rounded-lg px-3 py-2 font-semibold text-gray-700 hover:bg-ocean-50" href="{{ route('home') }}">{{ __('ui.home') }}</a>
            <div class="rounded-xl bg-red-50/70">
                <button class="flex w-full items-center justify-between rounded-xl px-3 py-2 font-bold text-red-950" type="button" @click="mobileExploreOpen = ! mobileExploreOpen"><span><i class="fa-solid fa-compass mr-2 text-red-700"></i>{{ __('Explorar') }}</span><i class="fa-solid fa-chevron-down text-xs transition-transform" :class="mobileExploreOpen && 'rotate-180'"></i></button>
                <div class="grid grid-cols-2 gap-1 px-2 pb-2" x-cloak x-show="mobileExploreOpen" x-transition>
                    <a class="mobile-submenu-link" href="{{ route('destinos') }}">{{ __('ui.destinations') }}</a><a class="mobile-submenu-link" href="{{ route('mapa.interactivo') }}">{{ __('nav.map') }}</a><a class="mobile-submenu-link" href="{{ route('municipios.index') }}">{{ __('ui.municipalities') }}</a><a class="mobile-submenu-link" href="{{ route('inspirame') }}">{{ __('ui.inspire') }}</a><a class="mobile-submenu-link" href="{{ route('tours-360.index') }}" target="_blank" rel="noopener">Tours 360°</a>
                </div>
            </div>
            <div class="rounded-xl bg-red-50/70">
                <button class="flex w-full items-center justify-between rounded-xl px-3 py-2 font-bold text-red-950" type="button" @click="mobileAgendaOpen = ! mobileAgendaOpen"><span><i class="fa-solid fa-calendar-days mr-2 text-red-700"></i>{{ __('Agenda') }}</span><i class="fa-solid fa-chevron-down text-xs transition-transform" :class="mobileAgendaOpen && 'rotate-180'"></i></button>
                <div class="grid grid-cols-3 gap-1 px-2 pb-2" x-cloak x-show="mobileAgendaOpen" x-transition><a class="mobile-submenu-link" href="{{ route('actividades.index') }}">{{ __('nav.activities') }}</a><a class="mobile-submenu-link" href="{{ route('eventos') }}">{{ __('ui.events') }}</a><a class="mobile-submenu-link" href="{{ route('noticias') }}">{{ __('ui.news') }}</a></div>
            </div>
            <a class="block rounded-lg px-3 py-2 font-semibold text-gray-700 hover:bg-ocean-50" href="{{ route('contacto') }}">{{ __('ui.contact') }}</a>
            <div class="rounded-xl bg-red-50/70">
                <button class="flex w-full items-center justify-between rounded-xl px-3 py-2 font-bold text-red-950" type="button" @click="mobileServicesOpen = ! mobileServicesOpen" :aria-expanded="mobileServicesOpen">
                    <span><i class="fa-solid fa-compass mr-2 text-red-700"></i>{{ __('Servicios turísticos') }}</span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="mobileServicesOpen && 'rotate-180'"></i>
                </button>
                <div class="space-y-1 px-2 pb-2" x-cloak x-show="mobileServicesOpen" x-transition>
                    @foreach($navServicios as $slug => $servicio)
                        <a class="flex items-center gap-3 rounded-lg bg-white/80 px-3 py-2 text-sm font-semibold text-gray-700" href="{{ route('servicios.show', $slug) }}">
                            <i class="fa-solid {{ $servicio['icono'] }} w-4 text-center" style="color: {{ $servicio['color'] }};"></i>
                            {{ $servicio['titulo'] }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="notranslate flex gap-2 pt-2" translate="no">
                <a class="flex-1 rounded-lg px-3 py-2 text-center font-semibold {{ app()->getLocale() === 'es' ? 'bg-ocean-700 text-white' : 'bg-ocean-50 text-ocean-700' }}" href="{{ route('idioma', 'es') }}">ES</a>
                <a class="flex-1 rounded-lg px-3 py-2 text-center font-semibold {{ app()->getLocale() === 'en' ? 'bg-ocean-700 text-white' : 'bg-ocean-50 text-ocean-700' }}" href="{{ route('idioma', 'en') }}">EN</a>
            </div>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const host = document.getElementById('services-menu-host');
        const button = document.getElementById('services-menu-button');
        const dropdown = document.getElementById('services-dropdown');
        const chevron = document.getElementById('services-menu-chevron');
        let closeTimer = null;

        if (!host || !button || !dropdown) return;

        const setOpen = (open) => {
            if (closeTimer) clearTimeout(closeTimer);
            dropdown.classList.toggle('is-open', open);
            dropdown.setAttribute('aria-hidden', open ? 'false' : 'true');
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            chevron?.classList.toggle('rotate-180', open);
        };

        const keepOpen = () => {
            if (closeTimer) clearTimeout(closeTimer);
            setOpen(true);
        };

        const scheduleClose = () => {
            if (closeTimer) clearTimeout(closeTimer);
            closeTimer = setTimeout(() => setOpen(false), 220);
        };

        setOpen(false);

        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            setOpen(!dropdown.classList.contains('is-open'));
        });

        button.addEventListener('mouseenter', keepOpen);
        button.addEventListener('mouseleave', scheduleClose);
        button.addEventListener('focus', keepOpen);
        dropdown.addEventListener('mouseenter', keepOpen);
        dropdown.addEventListener('mouseleave', scheduleClose);
        dropdown.addEventListener('focusin', keepOpen);
        dropdown.addEventListener('focusout', function (event) {
            if (!dropdown.contains(event.relatedTarget) && event.relatedTarget !== button) scheduleClose();
        });

        document.addEventListener('click', function (event) {
            if (!host.contains(event.target)) setOpen(false);
        });
    });
</script>
