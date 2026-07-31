@extends('layouts.app')

@section('title', 'Inspírame | Crea tu itinerario por Tarija')
@section('description', 'Planifica un itinerario personalizado por los municipios y atractivos turísticos de Tarija.')

@section('content')
<script>
    window.tarijaPlannerPlaces = @json($plannerPlaces);
</script>
<section
    class="min-h-screen bg-gradient-to-br from-amber-50 via-white to-emerald-50 pb-20 pt-28"
    x-data="itineraryPlanner({ places: window.tarijaPlannerPlaces || [], municipalities: @js($municipalities->pluck('nombre')->values()), generateUrl: @js(route('inspirame.generate')) })"
>
    <div class="container-custom">
        <div class="mx-auto max-w-5xl">
            <header class="mb-8 text-center">
                <span class="inline-flex items-center gap-2 rounded-full bg-red-100 px-4 py-2 text-xs font-black uppercase tracking-[.2em] text-red-800">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> {{ __('Planificador inteligente') }}
                </span>
                <h1 class="mt-4 text-4xl font-black text-gray-950 md:text-6xl">{{ app()->isLocale('en') ? 'Inspire me' : 'Inspírame' }} <span class="text-gradient">Tarija</span></h1>
                <p class="mx-auto mt-3 max-w-2xl text-lg text-gray-600">{{ __('Cuéntanos cómo quieres viajar y crearemos una ruta utilizando los lugares publicados desde el panel administrativo.') }}</p>
            </header>

            <div class="mb-7 flex items-center" aria-label="Progreso del planificador">
                <template x-for="number in 5" :key="number">
                    <div class="flex flex-1 items-center last:flex-none">
                        <button type="button" class="grid h-9 w-9 shrink-0 place-items-center rounded-full text-sm font-black transition" :class="step >= number ? 'bg-red-800 text-white shadow-lg shadow-red-900/20' : 'bg-gray-200 text-gray-500'" @click="number < step && (step = number)" x-text="number"></button>
                        <span x-show="number < 5" class="mx-2 h-0.5 flex-1 rounded-full" :class="step > number ? 'bg-gradient-to-r from-red-800 to-amber-500' : 'bg-gray-200'"></span>
                    </div>
                </template>
            </div>

            <div class="relative overflow-hidden rounded-[2rem] border border-red-100 bg-white shadow-2xl shadow-red-950/10">
                <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-red-800 via-orange-500 to-emerald-600"></div>
                <div class="min-h-[560px] p-6 md:p-12">
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="text-center"><i class="fa-solid fa-location-dot text-4xl text-red-700"></i><h2 class="mt-4 text-3xl font-black">{{ __('¿A dónde quieres ir?') }}</h2><p class="mt-2 text-gray-500">Selecciona uno o varios municipios de Tarija.</p></div>
                        <div class="relative mx-auto mt-8 max-w-xl">
                            <i class="fa-solid fa-search absolute left-5 top-1/2 -translate-y-1/2 text-red-700"></i>
                            <input class="w-full rounded-2xl border border-gray-200 bg-gray-50 py-4 pl-12 pr-4 outline-none transition focus:border-red-400 focus:bg-white focus:ring-4 focus:ring-red-100" x-model="destinationSearch" placeholder="Buscar municipio...">
                            <div class="absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl" x-show="destinationSearch && destinationOptions.length">
                                <template x-for="municipality in destinationOptions" :key="municipality"><button type="button" class="flex w-full items-center gap-3 px-5 py-3 text-left font-bold hover:bg-red-50" @click="selectDestination(municipality)"><i class="fa-solid fa-location-dot text-red-700"></i><span x-text="municipality"></span></button></template>
                            </div>
                        </div>
                        <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($municipalities as $municipality)
                                <button type="button" class="group relative overflow-hidden rounded-2xl border p-5 text-left transition hover:-translate-y-1 hover:shadow-xl" :class="destinations.includes(@js($municipality->nombre)) ? 'border-red-700 bg-red-800 text-white' : 'border-gray-100 bg-gray-50 hover:border-red-200 hover:bg-white'" @click="selectDestination(@js($municipality->nombre))">
                                    <span class="text-xs font-black uppercase tracking-widest opacity-60">Municipio</span><strong class="mt-2 block text-lg">{{ $municipality->nombre }}</strong><small class="mt-1 block opacity-70">{{ $municipality->provincia }}</small><i class="fa-solid fa-circle-check absolute bottom-5 right-5" x-show="destinations.includes(@js($municipality->nombre))"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div x-cloak x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="text-center"><i class="fa-regular fa-calendar text-4xl text-red-700"></i><h2 class="mt-4 text-3xl font-black">¿Cuándo quieres viajar?</h2><p class="mt-2 text-gray-500">Elige un mes aproximado o una fecha específica.</p></div>
                        <div class="mx-auto mt-8 max-w-3xl">
                            <label class="mb-3 block text-sm font-black uppercase tracking-widest text-gray-500">Fecha específica (opcional)</label>
                            <input type="date" class="w-full rounded-2xl border border-gray-200 bg-gray-50 p-4 outline-none focus:border-red-400" x-model="startDate" @input="months = []">
                            <div class="my-6 flex items-center gap-4 text-xs font-black uppercase tracking-widest text-gray-400"><span class="h-px flex-1 bg-gray-200"></span>o selecciona un mes<span class="h-px flex-1 bg-gray-200"></span></div>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $month)
                                    <button type="button" class="rounded-2xl border px-3 py-4 font-bold transition" :class="months.includes(@js($month)) ? 'border-red-800 bg-red-800 text-white shadow-lg' : 'border-gray-100 bg-gray-50 hover:border-red-200'" @click="toggleMonth(@js($month))"><i class="fa-regular fa-calendar mr-2"></i>{{ $month }}</button>
                                @endforeach
                            </div>
                            <p class="mt-3 text-center text-sm text-gray-500">Puedes seleccionar uno o dos meses.</p>
                            <div class="mt-8 flex items-center justify-center gap-5"><span class="font-bold">Duración:</span><button type="button" class="grid h-10 w-10 place-items-center rounded-full bg-gray-200" @click="duration = Math.max(1, duration - 1)"><i class="fa-solid fa-minus"></i></button><strong class="min-w-20 text-center text-lg"><span x-text="duration"></span> <span x-text="duration === 1 ? 'día' : 'días'"></span></strong><button type="button" class="grid h-10 w-10 place-items-center rounded-full bg-red-800 text-white" @click="duration = Math.min(7, duration + 1)"><i class="fa-solid fa-plus"></i></button></div>
                        </div>
                    </div>

                    {{--
                    Paso reservado para una futura personalización por compañía y ritmo.
                    <div x-cloak x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="text-center"><i class="fa-solid fa-people-group text-4xl text-red-700"></i><h2 class="mt-4 text-3xl font-black">¿Cómo viajas?</h2><p class="mt-2 text-gray-500">Adaptaremos la cantidad de actividades a tu compañía y ritmo.</p></div>
                        <div class="mx-auto mt-10 max-w-3xl"><div class="grid grid-cols-2 gap-4 md:grid-cols-4"><template x-for="option in [{id:'solo',label:'Solo',icon:'fa-person'},{id:'pareja',label:'En pareja',icon:'fa-heart'},{id:'familia',label:'En familia',icon:'fa-people-roof'},{id:'amigos',label:'Con amigos',icon:'fa-user-group'}]" :key="option.id"><button type="button" class="rounded-2xl border p-6 transition" :class="companion === option.id ? 'border-red-800 bg-red-800 text-white shadow-xl' : 'border-gray-100 bg-gray-50 hover:border-red-200'" @click="selectCompanion(option.id)"><i class="fa-solid text-3xl" :class="option.icon"></i><strong class="mt-3 block" x-text="option.label"></strong></button></template></div>
                        <h3 class="mb-4 mt-12 text-center text-xl font-black">¿A qué ritmo?</h3><div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4"><template x-for="option in [{id:'tranquilo',label:'Con calma',icon:'fa-person-walking'},{id:'activo',label:'Activo',icon:'fa-person-hiking'},{id:'intenso',label:'Intenso',icon:'fa-person-running'}]" :key="option.id"><button type="button" class="rounded-2xl border p-5 transition" :class="pace === option.id ? 'border-emerald-700 bg-emerald-700 text-white shadow-xl' : 'border-gray-100 bg-gray-50 hover:border-emerald-200'" @click="pace = option.id"><i class="fa-solid text-2xl" :class="option.icon"></i><strong class="mt-2 block" x-text="option.label"></strong></button></template></div></div>
                    </div>
                    --}}

                    <div x-cloak x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="text-center"><i class="fa-solid fa-icons text-4xl text-red-700"></i><h2 class="mt-4 text-3xl font-black">Tus intereses</h2><p class="mt-2 text-gray-500">Puedes elegir más de una experiencia.</p></div>
                        <div class="mx-auto mt-10 grid max-w-3xl gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($interests as $interest)
                                <button type="button" class="relative min-h-40 overflow-hidden rounded-3xl border p-6 text-left transition hover:-translate-y-1 hover:shadow-xl" :class="interests.includes({{ $interest->id }}) ? 'border-transparent text-white shadow-xl' : 'border-gray-100 bg-gray-50'" :style="interests.includes({{ $interest->id }}) ? 'background:linear-gradient(135deg, {{ $interest->color }}, #111827)' : ''" @click="toggleInterest({{ $interest->id }})"><i class="fa-solid {{ $interest->icono }} text-3xl"></i><strong class="mt-5 block text-lg">{{ $interest->nombre }}</strong><span class="mt-2 block text-xs opacity-70">{{ $interest->descripcion }}</span><i class="fa-solid fa-circle-check absolute right-4 top-4" x-show="interests.includes({{ $interest->id }})"></i></button>
                            @endforeach
                        </div>
                    </div>

                    <div x-cloak x-show="step === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="text-center"><i class="fa-solid fa-coins text-4xl text-red-700"></i><h2 class="mt-4 text-3xl font-black">¿Cuál es tu presupuesto?</h2><p class="mt-2 text-gray-500">Esto orientará el estilo general de las recomendaciones.</p></div>
                        <div class="mx-auto mt-12 grid max-w-2xl grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4"><template x-for="option in [{id:'economico',label:'Económico',icon:'fa-piggy-bank'},{id:'razonable',label:'Razonable',icon:'fa-wallet'},{id:'sin-limite',label:'Flexible',icon:'fa-gem'}]" :key="option.id"><button type="button" class="rounded-3xl border p-5 transition sm:p-7" :class="budget === option.id ? 'border-red-800 bg-red-800 text-white shadow-xl' : 'border-gray-100 bg-gray-50 hover:border-red-200'" @click="budget = option.id"><i class="fa-solid text-3xl" :class="option.icon"></i><strong class="mt-3 block sm:mt-4" x-text="option.label"></strong></button></template></div>
                        <p class="mx-auto mt-8 max-w-2xl rounded-2xl bg-sky-50 p-4 text-center text-sm text-sky-900"><i class="fa-solid fa-location-crosshairs mr-2"></i>Al crear el itinerario solicitaremos tu ubicación para iniciar el recorrido desde donde estés. Si no la compartes, usaremos la plaza principal (kilómetro cero).</p>
                    </div>

                    <div x-cloak x-show="step === 5" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="flex flex-col justify-between gap-5 border-b border-gray-100 pb-7 md:flex-row md:items-end"><div><span class="text-xs font-black uppercase tracking-[.2em] text-red-700">Tu viaje personalizado</span><h2 class="mt-2 text-3xl font-black"><span x-text="destinations.join(', ')"></span> en <span x-text="duration"></span> días</h2><p class="mt-2 text-gray-500"><span x-text="month || startDate"></span> · <span class="capitalize" x-text="companion"></span> · Ritmo <span x-text="pace"></span> · Presupuesto <span x-text="budget"></span></p></div><button type="button" class="rounded-full border border-red-200 px-5 py-3 font-bold text-red-800 hover:bg-red-50" @click="reset()"><i class="fa-solid fa-rotate-left mr-2"></i>Crear otro viaje</button></div>
                        <div class="mt-6 rounded-2xl bg-amber-50 p-5 text-amber-950" x-show="introduction"><i class="fa-solid fa-wand-magic-sparkles mr-2 text-amber-600"></i><span x-text="introduction"></span></div>
                        <details class="mt-6 rounded-2xl border border-gray-200 bg-white p-4" x-show="travelContext"><summary class="cursor-pointer font-black text-gray-800"><i class="fa-solid fa-circle-info mr-2 text-red-700"></i>Ver clima, fiestas y recomendaciones</summary><section class="mt-4 grid gap-4 md:grid-cols-2">
                            <article class="rounded-2xl border border-sky-100 bg-sky-50 p-5"><h3 class="font-black text-sky-950"><i class="fa-solid fa-cloud-sun mr-2"></i>Clima esperado</h3><div class="mt-3 space-y-3 text-sm"><template x-for="weather in (travelContext?.weather || [])" :key="weather.municipality"><div><strong x-text="weather.municipality"></strong><p class="text-sky-900" x-text="weather.summary"></p></div></template><p x-show="travelContext?.weatherNote" x-text="travelContext?.weatherNote"></p></div></article>
                            <article class="rounded-2xl border border-amber-100 bg-amber-50 p-5"><h3 class="font-black text-amber-950"><i class="fa-solid fa-church mr-2"></i>Fiestas y eventos</h3><div class="mt-3 space-y-2 text-sm"><template x-for="event in (travelContext?.events || [])" :key="event.title + event.date"><p><strong x-text="event.title"></strong><span class="block text-amber-900" x-text="event.date + (event.place ? ' · ' + event.place : '')"></span></p></template><template x-for="festival in (travelContext?.festivals || [])" :key="festival"><p x-text="festival"></p></template><p x-show="!(travelContext?.events || []).length && !(travelContext?.festivals || []).length">No hay celebraciones publicadas para el periodo. Conviene confirmar con el municipio.</p></div></article>
                        </section></details>
                        <nav class="mt-8 flex gap-3 overflow-x-auto pb-2" aria-label="Días del itinerario"><template x-for="day in itinerary" :key="day.day"><button type="button" class="min-w-28 rounded-2xl border px-5 py-3 text-left transition" :class="activeDay === day.day ? 'border-red-800 bg-red-800 text-white shadow-lg' : 'border-gray-200 bg-white hover:border-red-300'" @click="selectDay(day.day)"><small class="block font-black uppercase tracking-widest opacity-70">Itinerario</small><strong class="text-lg">Día <span x-text="day.day"></span></strong></button></template></nav>
                        <div class="mt-8 grid items-start gap-8 lg:grid-cols-[minmax(0,1.1fr)_minmax(340px,.9fr)]">
                            <div>
                                <template x-for="day in itinerary" :key="day.day">
                                    <article x-show="activeDay === day.day" x-transition>
                                        <div class="mb-4 flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-full bg-red-800 font-black text-white" x-text="day.day"></span><div><small class="font-black uppercase tracking-widest text-red-700">Día <span x-text="day.day"></span></small><h3 class="text-xl font-black" x-text="day.title || ('Descubre ' + destination)"></h3><p class="mt-1 text-sm text-gray-500" x-show="day.description" x-text="day.description"></p></div></div>
                                        <div class="grid gap-4"><template x-for="place in day.places" :key="place.id"><div class="group overflow-hidden rounded-3xl border border-gray-100 bg-gray-50 transition hover:-translate-y-1 hover:shadow-xl"><img class="h-44 w-full object-cover transition duration-500 group-hover:scale-105" :src="place.image" :alt="place.title"><div class="p-5"><small class="font-black uppercase tracking-wider text-red-700" x-text="place.subtype"></small><h4 class="mt-1 text-xl font-black" x-text="place.title"></h4><p class="mt-2 text-sm leading-6 text-gray-600" x-text="place.summary"></p><div class="mt-4 flex gap-2"><a class="rounded-full bg-red-800 px-4 py-2 text-xs font-black text-white" :href="'https://www.openstreetmap.org/?mlat=' + place.lat + '&mlon=' + place.lng + '#map=15/' + place.lat + '/' + place.lng" target="_blank" rel="noopener"><i class="fa-solid fa-map-location-dot mr-1"></i>Ver mapa</a><a class="rounded-full bg-white px-4 py-2 text-xs font-black text-gray-700 ring-1 ring-gray-200" :href="'https://www.google.com/maps/dir/?api=1&destination=' + place.lat + ',' + place.lng" target="_blank" rel="noopener">Cómo llegar</a></div></div></div></template></div>
                                    </article>
                                </template>
                            </div>
                            <aside class="sticky top-28 overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-xl">
                                <div class="border-b border-gray-100 p-5"><span class="text-xs font-black uppercase tracking-widest text-emerald-700">Ruta del día <span x-text="activeDay"></span></span><h3 class="mt-1 text-xl font-black" x-text="currentRouteStart ? 'Salida desde ' + currentRouteStart.title : 'Ruta diaria sugerida'"></h3><p class="mt-1 text-sm text-gray-500" x-text="currentLocation ? 'La ruta comienza desde tu ubicación actual.' : 'La ruta comienza en la plaza principal (kilómetro cero).'"></p></div>
                                <div id="itinerary-route-map" class="h-[360px] w-full bg-gray-100 sm:h-[460px] lg:h-[580px]"></div>
                                <p id="itinerary-route-summary" class="px-5 pt-4 text-center text-sm font-bold text-emerald-900"></p>
                                <a id="itinerary-google-route" class="m-4 flex items-center justify-center rounded-full bg-emerald-700 px-5 py-3 text-sm font-black text-white hover:bg-emerald-600" href="#" target="_blank" rel="noopener"><i class="fa-solid fa-route mr-2"></i>Abrir ruta completa</a>
                            </aside>
                        </div>
                        <div class="mt-10 border-t border-gray-100 pt-6 text-center" x-show="currentPackages.length">
                            <p class="text-sm text-gray-500">Hay opciones organizadas por operadoras para <strong x-text="currentDay?.municipality"></strong>.</p>
                            <button type="button" class="mt-3 inline-flex items-center rounded-full border border-emerald-200 bg-white px-5 py-2.5 text-sm font-black text-emerald-800 transition hover:bg-emerald-50" @click="showPackages = true">
                                <i class="fa-solid fa-suitcase-rolling mr-2"></i>Ver paquetes turísticos
                                <span class="ml-2 rounded-full bg-emerald-100 px-2 py-0.5 text-xs" x-text="currentPackages.length"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <footer class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-6 py-5 md:px-12" x-cloak x-show="step < 5">
                    <button type="button" class="rounded-full px-5 py-3 font-bold text-gray-600 hover:bg-white" x-show="step > 1" @click="previous()"><i class="fa-solid fa-arrow-left mr-2"></i>{{ __('Anterior') }}</button><span x-show="step === 1"></span>
                    <div class="flex items-center gap-4"><span class="hidden text-sm font-bold text-gray-400 sm:block" x-show="!errorMessage">{{ __('Paso') }} <span x-text="step"></span> {{ __('de') }} 4</span><span class="text-sm font-bold text-red-700" x-show="errorMessage" x-text="errorMessage"></span><button type="button" class="rounded-full bg-red-800 px-7 py-3 font-black text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-40" :disabled="!canContinue || generating" @click="next()"><i class="fa-solid fa-spinner fa-spin mr-2" x-show="generating"></i><span x-text="generating ? 'Creando con IA...' : (step === 4 ? @js(__('Crear mi itinerario')) : @js(__('Siguiente paso')))"></span><i class="fa-solid fa-arrow-right ml-2" x-show="!generating"></i></button></div>
                </footer>
            </div>
        </div>
    </div>

    <div x-cloak x-show="showPackages" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-950/65 p-4" role="dialog" aria-modal="true" aria-labelledby="packages-title" @keydown.escape.window="showPackages = false">
        <button type="button" class="absolute inset-0 cursor-default" aria-label="Cerrar paquetes turísticos" @click="showPackages = false"></button>
        <section class="relative max-h-[88vh] w-full max-w-4xl overflow-y-auto rounded-3xl bg-white shadow-2xl" @click.stop>
            <header class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-gray-100 bg-white/95 p-6 backdrop-blur">
                <div><span class="text-xs font-black uppercase tracking-[.2em] text-emerald-700">Día <span x-text="activeDay"></span> · <span x-text="currentDay?.municipality"></span></span><h2 id="packages-title" class="mt-1 text-2xl font-black">Paquetes turísticos disponibles</h2><p class="mt-1 text-sm text-gray-500">Solo se muestran opciones del municipio visitado durante este día.</p></div>
                <button type="button" class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200" aria-label="Cerrar" @click="showPackages = false"><i class="fa-solid fa-xmark"></i></button>
            </header>
            <div class="grid gap-4 p-6 md:grid-cols-2">
                <template x-for="tourPackage in currentPackages" :key="tourPackage.id">
                    <article class="flex gap-4 rounded-2xl border border-gray-200 p-4">
                        <img class="h-28 w-32 shrink-0 rounded-xl object-cover" :src="tourPackage.image" :alt="tourPackage.title">
                        <div class="min-w-0">
                            <small class="font-black uppercase tracking-wider text-emerald-700" x-text="tourPackage.provider"></small>
                            <h3 class="mt-1 font-black" x-text="tourPackage.title"></h3>
                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-gray-600" x-text="tourPackage.summary"></p>
                            <div class="mt-2 flex flex-wrap gap-1.5 text-xs font-bold"><span x-show="tourPackage.duration" x-text="tourPackage.duration"></span><span class="text-amber-800" x-show="tourPackage.price" x-text="tourPackage.price"></span></div>
                            <a class="mt-3 inline-flex text-xs font-black text-emerald-800 hover:underline" x-show="tourPackage.url" :href="tourPackage.url">Ver paquete y operadora <i class="fa-solid fa-arrow-right ml-1"></i></a>
                        </div>
                    </article>
                </template>
            </div>
        </section>
    </div>
</section>
@endsection

@push('scripts')
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
<script>
    (function () {
        let routeMap = null;
        const starts = {
            'Cercado': { title: 'Plaza Luis de Fuentes y Vargas', lat: -21.5353, lng: -64.7294 },
            'José María Avilés': { title: 'Plaza principal de Uriondo', lat: -21.6964, lng: -64.6567 },
            'Eustaquio Méndez': { title: 'Plaza principal de San Lorenzo', lat: -21.4169, lng: -64.7503 },
            'Aniceto Arce': { title: 'Plaza principal de Padcaya', lat: -21.8833, lng: -64.7147 },
            'Burdet O’Connor': { title: 'Plaza principal de Entre Ríos', lat: -21.5262, lng: -64.1735 },
            'Gran Chaco': { title: 'Plaza 12 de Agosto de Yacuiba', lat: -22.0139, lng: -63.6778 },
        };

        const loadLeaflet = (callback) => {
            if (window.L) return callback();
            const script = document.createElement('script');
            script.src = @js(asset('vendor/leaflet/leaflet.js'));
            script.onload = callback;
            document.head.appendChild(script);
        };

        const renderDayRoute = (detail) => {
            setTimeout(() => loadLeaflet(async function () {
                const canvas = document.getElementById('itinerary-route-map');
                if (!canvas) return;
                if (routeMap) { routeMap.remove(); routeMap = null; }

                const selectedDay = detail.itinerary.find(item => item.day === detail.day) || detail.itinerary[0];
                const visits = selectedDay?.places || [];
                if (!visits.length) return;
                const plaza = detail.routeStarts?.[selectedDay.municipality] || starts.Cercado;
                const start = detail.currentLocation || plaza;
                const points = [start, ...visits, start];
                routeMap = L.map(canvas, { scrollWheelZoom: true }).setView([start.lat, start.lng], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(routeMap);

                points.forEach((point, index) => {
                    if (index === points.length - 1) return;
                    const isCurrentLocation = detail.currentLocation && index === 0;
                    const isStart = index === 0;
                    const label = isCurrentLocation ? 'U' : (isStart ? '0' : index);
                    const icon = L.divIcon({ className: '', html: `<span style="display:grid;place-items:center;width:34px;height:34px;border-radius:50%;background:${isCurrentLocation ? '#047857' : '#991b1b'};color:white;font-weight:900;border:3px solid white;box-shadow:0 4px 12px #0005">${label}</span>`, iconSize: [34, 34], iconAnchor: [17, 17] });
                    L.marker([point.lat, point.lng], { icon }).addTo(routeMap).bindPopup(`<strong>${isCurrentLocation ? 'Tu ubicación · ' : (isStart ? 'Kilómetro cero · ' : 'Visita ' + label + ' · ')}${point.title}</strong>${point.address ? '<br>' + point.address : ''}`);
                });

                const routeSummary = document.getElementById('itinerary-route-summary');
                const osrmCoordinates = points.map(point => `${point.lng},${point.lat}`).join(';');
                try {
                    const response = await fetch(`https://router.project-osrm.org/route/v1/driving/${osrmCoordinates}?overview=full&geometries=geojson&steps=false`);
                    const data = await response.json();
                    if (!response.ok || data.code !== 'Ok' || !data.routes?.length) throw new Error('Ruta no disponible');
                    const roadCoordinates = data.routes[0].geometry.coordinates.map(([lng, lat]) => [lat, lng]);
                    L.polyline(roadCoordinates, { color: '#991b1b', weight: 6, opacity: .85 }).addTo(routeMap);
                    routeMap.fitBounds(L.latLngBounds(roadCoordinates), { padding: [35, 35], maxZoom: 14 });
                    if (routeSummary) routeSummary.textContent = `${(data.routes[0].distance / 1000).toFixed(1)} km · ${Math.round(data.routes[0].duration / 60)} min aproximados en carretera`;
                } catch (error) {
                    routeMap.fitBounds(L.latLngBounds(points.map(point => [point.lat, point.lng])), { padding: [35, 35], maxZoom: 14 });
                    if (routeSummary) routeSummary.textContent = 'No se pudo calcular el camino por carretera. Abre la ruta en Google Maps para verificarla.';
                }
                setTimeout(() => routeMap.invalidateSize(), 100);

                const routeLink = document.getElementById('itinerary-google-route');
                if (routeLink && points.length > 1) {
                    const destination = points[points.length - 1];
                    const waypoints = points.slice(1, -1).map(point => `${point.lat},${point.lng}`).join('|');
                    routeLink.href = `https://www.google.com/maps/dir/?api=1&origin=${start.lat},${start.lng}&destination=${destination.lat},${destination.lng}&waypoints=${encodeURIComponent(waypoints)}&travelmode=driving`;
                }
            }), 100);
        };

        window.addEventListener('itinerary:generated', event => renderDayRoute(event.detail));
        window.addEventListener('itinerary:day-selected', event => renderDayRoute(event.detail));
    })();

    window.addEventListener('load', function () {
        if (window.Alpine) return;

        document.addEventListener('alpine:init', function () {
            Alpine.data('itineraryPlanner', ({ places }) => ({
                step: 1, destinations: [], destinationSearch: '', months: [], startDate: '', duration: 2,
                companion: 'pareja', roomPreference: 'Matrimonial', pace: 'tranquilo', interests: [], hotelId: '', hotelSelections: {}, budget: 'razonable', itinerary: [], packages: [], showPackages: false, selectedHotels: {}, routeStarts: {}, currentLocation: null, activeDay: 1, travelContext: null, places,
                get destination() { return this.destinations.join(', '); },
                get month() { return this.months.join(' y '); },
                get destinationOptions() {
                    const query = this.destinationSearch.trim().toLowerCase();
                    const options = [...new Set(this.places.map(place => place.municipality))].filter(Boolean);
                    return query ? options.filter(name => name.toLowerCase().includes(query)) : options;
                },
                get currentHotel() { const day = this.itinerary.find(item => item.day === this.activeDay); return day ? this.selectedHotels[day.municipality] || null : null; },
                get currentRouteStart() { const day = this.itinerary.find(item => item.day === this.activeDay); return this.currentLocation || (day ? this.routeStarts[day.municipality] : null); },
                get currentDay() { return this.itinerary.find(item => item.day === this.activeDay) || null; },
                get currentPackages() { const municipality = this.currentDay?.municipality; return municipality ? this.packages.filter(tourPackage => (tourPackage.destinations || []).includes(municipality)) : []; },
                get hotels() { return this.places.filter(place => place.isHotel && (!this.destinations.length || this.destinations.includes(place.municipality))); },
                get canContinue() {
                    if (this.step === 1) return this.destinations.length > 0;
                    if (this.step === 2) return Boolean(this.months.length || this.startDate);
                    if (this.step === 3) return this.interests.length > 0;
                    return true;
                },
                selectDestination(name) { this.destinations = this.destinations.includes(name) ? this.destinations.filter(value => value !== name) : [...this.destinations, name]; this.destinationSearch = ''; },
                selectCompanion(id) { this.companion = id; this.roomPreference = ({ solo: 'Individual', pareja: 'Matrimonial', familia: 'Familiar', amigos: 'Dos camas' })[id]; this.hotelId = ''; },
                selectHotel(hotel) { this.hotelSelections = { ...this.hotelSelections, [hotel.municipality]: hotel.id }; },
                toggleInterest(id) { this.interests = this.interests.includes(id) ? this.interests.filter(value => value !== id) : [...this.interests, id]; },
                toggleMonth(name) { this.months = this.months.includes(name) ? this.months.filter(value => value !== name) : (this.months.length < 2 ? [...this.months, name] : this.months); this.startDate = ''; },
                async next() { if (!this.canContinue) return; if (this.step < 4) this.step++; else { await this.requestCurrentLocation(); this.generate(); } window.scrollTo({ top: 0, behavior: 'smooth' }); },
                requestCurrentLocation() {
                    if (!navigator.geolocation) return Promise.resolve(null);
                    return new Promise(resolve => navigator.geolocation.getCurrentPosition(position => {
                        this.currentLocation = { title: 'tu ubicación actual', lat: position.coords.latitude, lng: position.coords.longitude, kind: 'current-location' };
                        resolve(this.currentLocation);
                    }, () => { this.currentLocation = null; resolve(null); }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }));
                },
                previous() { if (this.step > 1) this.step--; },
                selectDay(day) { this.activeDay = day; setTimeout(() => window.dispatchEvent(new CustomEvent('itinerary:day-selected', { detail: { day, itinerary: this.itinerary, routeStarts: this.routeStarts, currentLocation: this.currentLocation } })), 50); },
                generate() {
                    let candidates = this.places.filter(place => !place.isHotel && this.destinations.includes(place.municipality));
                    if (!candidates.length) candidates = this.places.filter(place => !place.isHotel);
                    const preferred = candidates.filter(place => this.interests.includes(Number(place.typeId)));
                    const pool = (preferred.length ? preferred : candidates).sort((a, b) => Number(b.featured) - Number(a.featured));
                    const perDay = this.pace === 'intenso' ? 4 : this.pace === 'activo' ? 3 : 2;
                    const selected = [...pool, ...candidates.filter(place => !pool.some(item => item.id === place.id))].slice(0, this.duration * perDay);
                    this.itinerary = Array.from({ length: this.duration }, (_, day) => { const dayPlaces = selected.slice(day * perDay, (day + 1) * perDay); return { day: day + 1, municipality: dayPlaces[0]?.municipality, places: dayPlaces }; }).filter(item => item.places.length);
                    this.step = 5;
                    this.selectedHotels = Object.fromEntries(Object.entries(this.hotelSelections).map(([municipality, id]) => [municipality, this.places.find(place => place.id === Number(id))]).filter(([, hotel]) => hotel));
                    this.routeStarts = Object.fromEntries(this.destinations.map(municipality => [municipality, this.places.find(place => place.municipality === municipality && place.title.toLowerCase().includes('plaza')) || { title: 'Plaza Luis de Fuentes y Vargas', address: 'Centro de Tarija', lat: -21.5353, lng: -64.7294, kind: 'plaza' }]));
                    this.activeDay = 1;
                    setTimeout(() => window.dispatchEvent(new CustomEvent('itinerary:generated', { detail: { destinations: this.destinations, itinerary: this.itinerary, routeStarts: this.routeStarts, currentLocation: this.currentLocation, day: 1 } })), 250);
                },
                reset() {
                    this.step = 1; this.destinations = []; this.destinationSearch = ''; this.months = []; this.startDate = '';
                    this.duration = 2; this.companion = 'pareja'; this.roomPreference = 'Matrimonial'; this.pace = 'tranquilo'; this.interests = [];
                    this.hotelId = ''; this.hotelSelections = {}; this.selectedHotels = {}; this.routeStarts = {}; this.currentLocation = null; this.budget = 'razonable'; this.itinerary = []; this.packages = []; this.showPackages = false;
                },
            }));
        }, { once: true });

        const script = document.createElement('script');
        script.src = @js(asset('vendor/alpine/alpine.min.js'));
        document.head.appendChild(script);
    }, { once: true });
</script>
@endpush
