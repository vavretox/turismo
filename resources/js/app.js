import './bootstrap';
import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import AOS from 'aos';
import 'aos/dist/aos.css';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.Alpine = Alpine;
window.L = L;

Alpine.data('tourismWidget', ({ chatUrl, newsletterUrl }) => ({
    open: false,
    tab: 'chat',
    message: '',
    loading: false,
    newsletterLoading: false,
    newsletterStatus: '',
    newsletterError: false,
    subscriber: { name: '', email: '', consent: false },
    messages: [{ role: 'bot', text: '¡Hola! Soy tu guía virtual. Pregúntame por destinos, municipios, eventos o experiencias publicadas en este portal.', results: [] }],
    csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    },
    scrollMessages() {
        this.$nextTick(() => { this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight; });
    },
    askSuggestion(text) {
        this.message = text;
        this.sendMessage();
    },
    async sendMessage() {
        const text = this.message.trim();
        if (text.length < 2 || this.loading) return;
        this.messages.push({ role: 'user', text, results: [] });
        this.message = '';
        this.loading = true;
        this.scrollMessages();
        try {
            const response = await fetch(chatUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                body: JSON.stringify({ message: text }),
            });
            const data = await response.json();
            this.messages.push({ role: 'bot', text: data.answer || 'No pude procesar la consulta.', results: data.results || [] });
        } catch (error) {
            this.messages.push({ role: 'bot', text: 'No pude conectarme en este momento. Intenta nuevamente en unos segundos.', results: [] });
        } finally {
            this.loading = false;
            this.scrollMessages();
        }
    },
    async subscribe() {
        if (this.newsletterLoading) return;
        this.newsletterLoading = true;
        this.newsletterStatus = '';
        this.newsletterError = false;
        try {
            const response = await fetch(newsletterUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                body: JSON.stringify({ email: this.subscriber.email, nombre: this.subscriber.name, consentimiento: this.subscriber.consent }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || 'No se pudo completar la suscripción.');
            this.newsletterStatus = data.message;
            this.subscriber = { name: '', email: '', consent: false };
        } catch (error) {
            this.newsletterError = true;
            this.newsletterStatus = error.message || 'No se pudo completar la suscripción.';
        } finally {
            this.newsletterLoading = false;
        }
    },
}));

Alpine.data('itineraryPlanner', ({ places, municipalities = [], generateUrl }) => ({
    step: 1,
    destinations: [],
    destinationSearch: '',
    months: [],
    startDate: '',
    duration: 2,
    companion: 'pareja',
    roomPreference: 'Matrimonial',
    pace: 'tranquilo',
    interests: [],
    hotelId: '',
    hotelSelections: {},
    budget: 'razonable',
    itinerary: [],
    packages: [],
    showPackages: false,
    introduction: '',
    travelContext: null,
    selectedHotels: {},
    routeStarts: {},
    currentLocation: null,
    activeDay: 1,
    generating: false,
    errorMessage: '',
    places,
    municipalities,
    get destination() {
        return this.destinations.join(', ');
    },
    get month() {
        return this.months.join(' y ');
    },
    get destinationOptions() {
        const query = this.destinationSearch.trim().toLowerCase();
        return query ? this.municipalities.filter(name => name.toLowerCase().includes(query)) : this.municipalities;
    },
    get hotels() {
        return this.places
            .filter(place => place.isHotel && (!this.destinations.length || this.destinations.includes(place.municipality)))
            .sort((a, b) => Number((b.roomOptions || []).includes(this.roomPreference)) - Number((a.roomOptions || []).includes(this.roomPreference)));
    },
    get currentHotel() {
        const day = this.itinerary.find(item => item.day === this.activeDay);
        return day ? this.selectedHotels[day.municipality] || null : null;
    },
    get currentRouteStart() {
        const day = this.itinerary.find(item => item.day === this.activeDay);
        return this.currentLocation || (day ? this.routeStarts[day.municipality] : null);
    },
    get currentDay() {
        return this.itinerary.find(item => item.day === this.activeDay) || null;
    },
    get currentPackages() {
        const municipality = this.currentDay?.municipality;
        return municipality
            ? this.packages.filter(tourPackage => (tourPackage.destinations || []).includes(municipality))
            : [];
    },
    get canContinue() {
        if (this.step === 1) return this.destinations.length > 0;
        if (this.step === 2) return Boolean(this.months.length || this.startDate);
        if (this.step === 3) return this.interests.length > 0;
        return true;
    },
    selectDestination(name) {
        this.destinations = this.destinations.includes(name)
            ? this.destinations.filter(value => value !== name)
            : [...this.destinations, name];
        this.destinationSearch = '';
    },
    selectCompanion(id) {
        this.companion = id;
        this.roomPreference = ({ solo: 'Individual', pareja: 'Matrimonial', familia: 'Familiar', amigos: 'Dos camas' })[id];
        this.hotelId = '';
        this.hotelSelections = {};
    },
    selectHotel(hotel) {
        this.hotelSelections = { ...this.hotelSelections, [hotel.municipality]: hotel.id };
    },
    toggleInterest(id) {
        this.interests = this.interests.includes(id)
            ? this.interests.filter(value => value !== id)
            : [...this.interests, id];
    },
    toggleMonth(name) {
        if (this.months.includes(name)) {
            this.months = this.months.filter(value => value !== name);
        } else if (this.months.length < 2) {
            this.months = [...this.months, name];
        }
        this.startDate = '';
    },
    async next() {
        if (!this.canContinue) return;
        if (this.step < 4) {
            this.step++;
        } else {
            await this.requestCurrentLocation();
            await this.generate();
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    requestCurrentLocation() {
        if (!navigator.geolocation) return Promise.resolve(null);

        return new Promise(resolve => {
            navigator.geolocation.getCurrentPosition(
                position => {
                    this.currentLocation = {
                        title: 'tu ubicación actual',
                        lat: position.coords.latitude,
                        lng: position.coords.longitude,
                        kind: 'current-location',
                    };
                    resolve(this.currentLocation);
                },
                () => {
                    this.currentLocation = null;
                    resolve(null);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 },
            );
        });
    },
    previous() {
        if (this.step > 1) this.step--;
    },
    selectDay(day) {
        this.activeDay = day;
        setTimeout(() => window.dispatchEvent(new CustomEvent('itinerary:day-selected', {
            detail: { day, itinerary: this.itinerary, routeStarts: this.routeStarts, currentLocation: this.currentLocation },
        })), 50);
    },
    async generate() {
        if (this.generating) return;
        this.generating = true;
        this.errorMessage = '';
        try {
            const response = await fetch(generateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    destinations: this.destinations,
                    months: this.months,
                    startDate: this.startDate || null,
                    duration: this.duration,
                    companion: this.companion,
                    roomPreference: this.roomPreference,
                    pace: this.pace,
                    interests: this.interests,
                    hotelId: this.hotelId || null,
                    hotelIds: this.hotelSelections,
                    budget: this.budget,
                }),
            });
            const data = await response.json();
            if (!response.ok) {
                const validationError = data.errors ? Object.values(data.errors).flat()[0] : null;
                throw new Error(validationError || data.message || 'No se pudo crear el itinerario.');
            }
            this.itinerary = data.itinerary || [];
            this.packages = data.packages || [];
            this.introduction = data.introduction || '';
            this.travelContext = data.travelContext || null;
            this.selectedHotels = data.hotels || {};
            this.routeStarts = data.routeStarts || {};
            this.activeDay = 1;
            this.step = 5;
            setTimeout(() => window.dispatchEvent(new CustomEvent('itinerary:generated', { detail: { destinations: this.destinations, itinerary: this.itinerary, routeStarts: this.routeStarts, currentLocation: this.currentLocation, day: 1 } })), 250);
        } catch (error) {
            this.errorMessage = error.message || 'No se pudo crear el itinerario. Intenta nuevamente.';
        } finally {
            this.generating = false;
        }
    },
    reset() {
        this.step = 1;
        this.destinations = [];
        this.destinationSearch = '';
        this.months = [];
        this.startDate = '';
        this.duration = 2;
        this.companion = 'pareja';
        this.roomPreference = 'Matrimonial';
        this.pace = 'tranquilo';
        this.interests = [];
        this.hotelId = '';
        this.hotelSelections = {};
        this.budget = 'razonable';
        this.itinerary = [];
        this.packages = [];
        this.showPackages = false;
        this.introduction = '';
        this.travelContext = null;
        this.selectedHotels = {};
        this.routeStarts = {};
        this.currentLocation = null;
        this.activeDay = 1;
        this.errorMessage = '';
    },
}));

Alpine.start();

AOS.init({ duration: 700, once: true, offset: 80 });

window.initializeSwiper = (selector, options = {}) => {
    if (!document.querySelector(selector)) {
        return null;
    }

    return new Swiper(selector, {
        modules: [Navigation, Pagination, Autoplay, EffectFade],
        loop: true,
        autoplay: { delay: 4500, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        ...options,
    });
};

window.initializeMap = (elementId, coordinates, options = {}) => {
    const element = document.getElementById(elementId);

    if (!element || element.dataset.loaded === 'true') {
        return null;
    }

    element.dataset.loaded = 'true';

    const map = L.map(elementId, {
        scrollWheelZoom: false,
    }).setView(coordinates, options.zoom || 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    L.marker(coordinates).addTo(map)
        .bindPopup(options.popup || 'Punto turistico')
        .openPopup();

    return map;
};

window.initializeDestinationRouteMap = (elementId, routes, destination) => {
    const element = document.getElementById(elementId);
    if (!element || !window.L || !destination?.lat || !destination?.lng) return null;

    const map = L.map(element, { scrollWheelZoom: false }).setView([destination.lat, destination.lng], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    let routeLayer = null;
    let checkpointLayer = null;
    let renderVersion = 0;

    const checkpointIcon = (label, type = 'checkpoint') => L.divIcon({
        className: 'destination-checkpoint-wrapper',
        html: `<span class="destination-checkpoint destination-checkpoint-${type}">${label}</span>`,
        iconSize: [38, 38],
        iconAnchor: [19, 19],
        popupAnchor: [0, -22],
    });

    const routingEndpoint = (transport) => {
        if (transport === 'caminata') return 'https://routing.openstreetmap.de/routed-foot';
        if (transport === 'bicicleta') return 'https://routing.openstreetmap.de/routed-bike';
        return 'https://routing.openstreetmap.de/routed-car';
    };

    const requestRoadGeometry = async (from, to, transport) => {
        if (transport === 'avion') return null;
        const base = routingEndpoint(transport);
        const coordinates = `${from[1]},${from[0]};${to[1]},${to[0]}`;
        const response = await fetch(`${base}/route/v1/driving/${coordinates}?overview=full&geometries=geojson`);
        if (!response.ok) throw new Error(`Routing service returned ${response.status}`);
        const data = await response.json();
        if (data.code !== 'Ok' || !data.routes?.[0]?.geometry?.coordinates) return null;
        return data.routes[0].geometry.coordinates.map(([lng, lat]) => [lat, lng]);
    };

    const render = async (index) => {
        const currentVersion = ++renderVersion;
        if (routeLayer) map.removeLayer(routeLayer);
        if (checkpointLayer) map.removeLayer(checkpointLayer);
        const route = routes[index] || {};
        const points = [];
        const markers = [];
        const legs = [];
        let previousPoint = null;

        if (route.origen_latitud && route.origen_longitud) {
            const originPoint = [Number(route.origen_latitud), Number(route.origen_longitud)];
            points.push(originPoint);
            previousPoint = originPoint;
            markers.push(L.marker(originPoint, { icon: checkpointIcon('S', 'start') })
                .bindPopup(`<strong>Salida</strong><br>${route.origen || 'Punto de origen'}`));
        }

        (route.tramos || []).forEach((segment, segmentIndex) => {
            if (!segment.latitud || !segment.longitud) return;
            const point = [Number(segment.latitud), Number(segment.longitud)];
            points.push(point);
            if (previousPoint) legs.push({ from: previousPoint, to: point, transport: segment.medio || 'auto' });
            previousPoint = point;
            const duration = segment.duracion ? `<br><small>${segment.duracion}</small>` : '';
            markers.push(L.marker(point, { icon: checkpointIcon(String(segmentIndex + 1)) })
                .bindPopup(`<strong>Punto de control ${segmentIndex + 1}</strong><br>${segment.hasta || destination.name}${duration}`));
        });

        const lastSegment = (route.tramos || []).at(-1);
        if ((!lastSegment?.latitud || !lastSegment?.longitud) && destination.lat && destination.lng) {
            const destinationPoint = [Number(destination.lat), Number(destination.lng)];
            points.push(destinationPoint);
            markers.push(L.marker(destinationPoint, { icon: checkpointIcon(String((route.tramos || []).length), 'finish') })
                .bindPopup(`<strong>Meta</strong><br>${destination.name}`));
        }

        const validPoints = points.filter(([lat, lng]) => Number.isFinite(lat) && Number.isFinite(lng));
        checkpointLayer = L.layerGroup(markers).addTo(map);
        if (validPoints.length > 1) map.fitBounds(L.latLngBounds(validPoints), { padding: [45, 45] });
        else map.setView([destination.lat, destination.lng], 8);

        element.classList.add('is-routing');
        const results = await Promise.allSettled(legs.map((leg) => requestRoadGeometry(leg.from, leg.to, leg.transport)));
        if (currentVersion !== renderVersion) return;

        const roadLines = [];
        let unavailable = 0;
        results.forEach((result, legIndex) => {
            if (result.status === 'fulfilled' && result.value?.length > 1) {
                roadLines.push(L.polyline(result.value, {
                    color: '#991b1b',
                    weight: 5,
                    opacity: .88,
                    lineCap: 'round',
                    lineJoin: 'round',
                }));
            } else if (legs[legIndex]?.transport === 'avion') {
                roadLines.push(L.polyline([legs[legIndex].from, legs[legIndex].to], {
                    color: '#64748b',
                    weight: 3,
                    opacity: .75,
                    dashArray: '8 10',
                }));
            } else {
                unavailable++;
            }
        });

        routeLayer = L.layerGroup(roadLines).addTo(map);
        element.classList.remove('is-routing');
        element.dataset.routingMessage = unavailable
            ? `${unavailable} tramo(s) sin camino disponible en OpenStreetMap`
            : '';
    };

    render(0);
    window.addEventListener('destination-route:change', (event) => render(event.detail.index));
    setTimeout(() => map.invalidateSize(), 150);
    return map;
};

let googleMapsLoader;

const loadGoogleMaps = (apiKey) => {
    if (window.google?.maps) {
        return Promise.resolve(window.google.maps);
    }

    if (googleMapsLoader) {
        return googleMapsLoader;
    }

    googleMapsLoader = new Promise((resolve, reject) => {
        const callbackName = `initGoogleMapsTarija${Date.now()}`;
        const script = document.createElement('script');

        window[callbackName] = () => {
            resolve(window.google.maps);
            delete window[callbackName];
        };

        script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&callback=${callbackName}&v=weekly`;
        script.async = true;
        script.defer = true;
        script.onerror = () => reject(new Error('No se pudo cargar Google Maps.'));

        document.head.appendChild(script);
    });

    return googleMapsLoader;
};

const tarijaMunicipios = [
    {
        name: 'Tarija',
        province: 'Cercado',
        color: '#991b1b',
        position: { lat: -21.5355, lng: -64.7296 },
        path: [
            { lat: -21.36, lng: -64.88 },
            { lat: -21.34, lng: -64.61 },
            { lat: -21.58, lng: -64.53 },
            { lat: -21.75, lng: -64.72 },
            { lat: -21.62, lng: -64.94 },
        ],
        summary: 'Centro urbano, plazas, miradores, gastronomia chapaca y acceso a circuitos culturales.',
        interests: ['Centro historico', 'Mirador de los Suenos', 'Gastronomia local'],
    },
    {
        name: 'Uriondo',
        province: 'Avilez',
        color: '#b91c1c',
        position: { lat: -21.7038, lng: -64.6563 },
        path: [
            { lat: -21.63, lng: -64.86 },
            { lat: -21.73, lng: -64.71 },
            { lat: -21.85, lng: -64.57 },
            { lat: -22.01, lng: -64.71 },
            { lat: -21.86, lng: -64.99 },
        ],
        summary: 'Capital de la Ruta del Vino: vinedos, bodegas, singani, gastronomia y paisajes del valle.',
        interests: ['Ruta del Vino', 'Bodegas y vinedos', 'Valle de la Concepcion'],
        destination: 'ruta-del-vino',
    },
    {
        name: 'San Lorenzo',
        province: 'Mendez',
        color: '#7f1d1d',
        position: { lat: -21.4168, lng: -64.7579 },
        path: [
            { lat: -21.05, lng: -65.06 },
            { lat: -21.05, lng: -64.72 },
            { lat: -21.34, lng: -64.60 },
            { lat: -21.36, lng: -64.88 },
            { lat: -21.18, lng: -65.05 },
        ],
        summary: 'Pueblos tradicionales, historia, arquitectura colonial y paisajes cercanos al valle central.',
        interests: ['Casa Vieja', 'Cultura chapaca', 'Arquitectura tradicional'],
        destination: 'casa-vieja',
    },
    {
        name: 'Padcaya',
        province: 'Aniceto Arce',
        color: '#dc2626',
        position: { lat: -21.8841, lng: -64.7119 },
        path: [
            { lat: -21.78, lng: -64.57 },
            { lat: -22.04, lng: -64.50 },
            { lat: -22.30, lng: -64.72 },
            { lat: -22.21, lng: -65.05 },
            { lat: -21.86, lng: -64.99 },
            { lat: -22.01, lng: -64.71 },
        ],
        summary: 'Naturaleza, rutas hacia valles y acceso a zonas protegidas del sur tarijeno.',
        interests: ['Reserva de Sama', 'Paisajes rurales', 'Rutas naturales'],
        destination: 'reserva-biologica-cordillera-de-sama',
    },
    {
        name: 'Bermejo',
        province: 'Aniceto Arce',
        color: '#ef4444',
        position: { lat: -22.7322, lng: -64.3373 },
        path: [
            { lat: -22.34, lng: -64.72 },
            { lat: -22.40, lng: -64.36 },
            { lat: -22.78, lng: -64.18 },
            { lat: -22.94, lng: -64.40 },
            { lat: -22.74, lng: -64.78 },
        ],
        summary: 'Ciudad fronteriza, rios, comercio y clima calido con identidad del sur.',
        interests: ['Rio Bermejo', 'Frontera sur', 'Gastronomia local'],
    },
    {
        name: 'Yacuiba',
        province: 'Gran Chaco',
        color: '#991b1b',
        position: { lat: -22.0159, lng: -63.6775 },
        path: [
            { lat: -21.75, lng: -63.95 },
            { lat: -21.79, lng: -63.54 },
            { lat: -22.16, lng: -63.36 },
            { lat: -22.31, lng: -63.70 },
            { lat: -22.10, lng: -64.02 },
        ],
        summary: 'Puerta del Chaco tarijeno, comercio, cultura fronteriza y gastronomia chaquena.',
        interests: ['Chaco tarijeno', 'Cultura fronteriza', 'Gastronomia chaquena'],
    },
    {
        name: 'Carapari',
        province: 'Gran Chaco',
        color: '#b91c1c',
        position: { lat: -21.8284, lng: -63.7468 },
        path: [
            { lat: -21.45, lng: -64.12 },
            { lat: -21.48, lng: -63.70 },
            { lat: -21.80, lng: -63.53 },
            { lat: -21.75, lng: -63.95 },
            { lat: -21.96, lng: -64.15 },
            { lat: -21.66, lng: -64.24 },
        ],
        summary: 'Naturaleza chaquena, rios, pozas y circuitos de aventura.',
        interests: ['Aguas termales', 'Rios y pozas', 'Naturaleza chaquena'],
    },
    {
        name: 'Villa Montes',
        province: 'Gran Chaco',
        color: '#7f1d1d',
        position: { lat: -21.2625, lng: -63.469 },
        path: [
            { lat: -20.75, lng: -63.98 },
            { lat: -20.78, lng: -63.18 },
            { lat: -21.30, lng: -62.95 },
            { lat: -21.62, lng: -63.32 },
            { lat: -21.48, lng: -63.70 },
            { lat: -21.45, lng: -64.12 },
        ],
        summary: 'Historia chaquena, rio Pilcomayo, pesca y paisajes secos del Chaco.',
        interests: ['Rio Pilcomayo', 'Historia del Chaco', 'Pesca y naturaleza'],
    },
    {
        name: 'Entre Rios',
        province: "O'Connor",
        color: '#dc2626',
        position: { lat: -21.5266, lng: -64.1723 },
        path: [
            { lat: -21.10, lng: -64.48 },
            { lat: -21.14, lng: -64.12 },
            { lat: -21.45, lng: -64.12 },
            { lat: -21.66, lng: -64.24 },
            { lat: -21.74, lng: -64.50 },
            { lat: -21.58, lng: -64.53 },
            { lat: -21.34, lng: -64.61 },
        ],
        summary: 'Bosques, rios, cultura guarani y rutas naturales hacia la serrania.',
        interests: ['Rios y serranias', 'Cultura guarani', 'Naturaleza'],
    },
    {
        name: 'El Puente',
        province: 'Mendez',
        color: '#991b1b',
        position: { lat: -20.9784, lng: -65.209 },
        path: [
            { lat: -20.55, lng: -65.40 },
            { lat: -20.58, lng: -65.02 },
            { lat: -21.05, lng: -64.72 },
            { lat: -21.05, lng: -65.06 },
            { lat: -20.92, lng: -65.37 },
        ],
        summary: 'Paisajes de altura, comunidades rurales y rutas hacia la zona norte.',
        interests: ['Paisajes rurales', 'Comunidades', 'Rutas de altura'],
    },
    {
        name: 'Yunchara',
        province: 'Avilez',
        color: '#b91c1c',
        position: { lat: -21.7667, lng: -65.1833 },
        path: [
            { lat: -21.18, lng: -65.05 },
            { lat: -21.62, lng: -64.94 },
            { lat: -21.86, lng: -64.99 },
            { lat: -22.21, lng: -65.05 },
            { lat: -22.08, lng: -65.45 },
            { lat: -21.40, lng: -65.42 },
        ],
        summary: 'Altiplano tarijeno, lagunas, camelidos y paisajes de altura.',
        interests: ['Lagunas altoandinas', 'Reserva de Sama', 'Paisajes de altura'],
    },
];

const tarijaProvincias = [
    {
        name: 'Cercado',
        summary: 'Provincia central de Tarija, concentra la ciudad capital, miradores, plazas historicas, gastronomia y servicios para iniciar rutas turisticas.',
        municipalities: ['Tarija'],
        interests: ['Centro historico', 'Miradores urbanos', 'Gastronomia chapaca'],
    },
    {
        name: 'Jose Maria Aviles',
        summary: 'Zona vitivinicola del valle tarijeno, con Uriondo como punto clave para bodegas, vinedos y experiencias gastronomicas.',
        municipalities: ['Uriondo', 'Yunchara'],
        interests: ['Ruta del Vino', 'Valle de la Concepcion', 'Paisajes altoandinos'],
        destination: 'ruta-del-vino',
    },
    {
        name: 'Eustaquio Mendez',
        summary: 'Provincia de pueblos tradicionales, arquitectura colonial, historia chapaca y caminos rurales hacia el norte del departamento.',
        municipalities: ['San Lorenzo', 'El Puente'],
        interests: ['Casa Vieja', 'Cultura chapaca', 'Comunidades rurales'],
        destination: 'casa-vieja',
    },
    {
        name: 'Aniceto Arce',
        summary: 'Conecta valles, frontera sur y rutas naturales; incluye Padcaya, Bermejo y accesos a paisajes de gran diversidad.',
        municipalities: ['Padcaya', 'Bermejo'],
        interests: ['Reserva de Sama', 'Rio Bermejo', 'Rutas naturales'],
        destination: 'reserva-biologica-cordillera-de-sama',
    },
    {
        name: "Burdet O'Connor",
        summary: 'Territorio de rios, serranias, bosques y cultura guarani, ideal para experiencias de naturaleza y recorridos comunitarios.',
        municipalities: ['Entre Rios'],
        interests: ['Rios y serranias', 'Cultura guarani', 'Naturaleza'],
    },
    {
        name: 'Gran Chaco',
        summary: 'La region chaquena de Tarija: energia, frontera, historia, Pilcomayo, gastronomia chaquena y paisajes calidos.',
        municipalities: ['Yacuiba', 'Carapari', 'Villa Montes'],
        interests: ['Rio Pilcomayo', 'Cultura chaquena', 'Frontera y comercio'],
    },
];

const tarijaMapStyle = [
    { featureType: 'administrative.province', elementType: 'geometry.stroke', stylers: [{ color: '#7f1d1d' }, { weight: 1.4 }] },
    { featureType: 'landscape', elementType: 'geometry', stylers: [{ color: '#f8f1e7' }] },
    { featureType: 'poi', stylers: [{ visibility: 'off' }] },
    { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
    { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#6b7280' }] },
    { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#dbeafe' }] },
];

const initializeTourismWidget = () => {
    const widget = document.getElementById('tourism-widget');
    if (!widget || widget.dataset.initialized === 'true') return;
    widget.dataset.initialized = 'true';

    const panel = document.getElementById('tourism-widget-panel');
    const chatPane = document.getElementById('widget-chat-pane');
    const newsletterPane = document.getElementById('widget-newsletter-pane');
    const tabButtons = [...widget.querySelectorAll('[data-widget-tab]')];
    const messages = document.getElementById('tourism-chat-messages');
    const chatForm = document.getElementById('tourism-chat-form');
    const chatInput = document.getElementById('tourism-chat-input');
    const newsletterForm = document.getElementById('tourism-newsletter-form');
    const newsletterStatus = document.getElementById('newsletter-status');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const selectTab = (tab) => {
        chatPane.hidden = tab !== 'chat';
        newsletterPane.hidden = tab !== 'newsletter';
        tabButtons.forEach((button) => button.classList.toggle('is-active', button.dataset.widgetTab === tab));
    };

    const openPanel = (tab) => {
        selectTab(tab);
        panel.hidden = false;
        panel.classList.add('is-visible');
        if (tab === 'chat') setTimeout(() => chatInput.focus(), 80);
    };

    const closePanel = () => {
        panel.classList.remove('is-visible');
        panel.hidden = true;
    };

    widget.querySelectorAll('[data-widget-open]').forEach((button) => {
        button.addEventListener('click', () => openPanel(button.dataset.widgetOpen));
    });
    tabButtons.forEach((button) => button.addEventListener('click', () => selectTab(button.dataset.widgetTab)));
    document.getElementById('tourism-widget-close')?.addEventListener('click', closePanel);
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closePanel(); });

    const addMessage = (text, role, results = []) => {
        const bubble = document.createElement('div');
        bubble.className = role === 'user' ? 'chat-message-user' : 'chat-message-bot';
        const paragraph = document.createElement('p');
        paragraph.textContent = text;
        bubble.appendChild(paragraph);

        results.forEach((result) => {
            const link = document.createElement('a');
            link.className = 'chat-result mt-2';
            link.href = result.url;
            const type = document.createElement('span');
            type.className = 'text-[10px] font-black uppercase tracking-wider text-red-700';
            type.textContent = result.type;
            const title = document.createElement('strong');
            title.className = 'block text-sm';
            title.textContent = result.title;
            const summary = document.createElement('span');
            summary.className = 'mt-1 block text-xs leading-5 text-gray-500';
            summary.textContent = result.summary || '';
            link.append(type, title, summary);
            bubble.appendChild(link);
        });
        messages.appendChild(bubble);
        messages.scrollTop = messages.scrollHeight;
        return bubble;
    };

    const ask = async (question) => {
        const text = question.trim();
        if (text.length < 2 || chatForm.dataset.loading === 'true') return;
        addMessage(text, 'user');
        chatInput.value = '';
        chatForm.dataset.loading = 'true';
        const loading = addMessage('Buscando en el portal...', 'bot');
        try {
            const response = await fetch(widget.dataset.chatUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ message: text }),
            });
            const data = await response.json();
            loading.remove();
            addMessage(data.answer || 'No pude procesar la consulta.', 'bot', data.results || []);
        } catch (error) {
            loading.remove();
            addMessage('No pude conectarme en este momento. Intenta nuevamente.', 'bot');
        } finally {
            chatForm.dataset.loading = 'false';
        }
    };

    chatForm.addEventListener('submit', (event) => { event.preventDefault(); ask(chatInput.value); });
    widget.querySelectorAll('[data-question]').forEach((button) => button.addEventListener('click', () => ask(button.dataset.question)));

    newsletterForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = newsletterForm.querySelector('button[type="submit"]');
        submit.disabled = true;
        newsletterStatus.classList.add('hidden');
        try {
            const response = await fetch(widget.dataset.newsletterUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({
                    nombre: document.getElementById('newsletter-name').value,
                    email: document.getElementById('newsletter-email').value,
                    consentimiento: document.getElementById('newsletter-consent').checked,
                }),
            });
            const data = await response.json();
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || 'No se pudo completar la suscripción.');
            newsletterStatus.textContent = data.message;
            newsletterStatus.className = 'rounded-xl bg-green-500/20 px-3 py-2 text-sm text-green-100';
            newsletterForm.reset();
        } catch (error) {
            newsletterStatus.textContent = error.message || 'No se pudo completar la suscripción.';
            newsletterStatus.className = 'rounded-xl bg-red-500/20 px-3 py-2 text-sm text-red-100';
        } finally {
            submit.disabled = false;
        }
    });
};

window.initializeTarijaGoogleMap = () => {
    const element = document.getElementById('tarija-illustrated-map');
    const info = document.getElementById('municipio-info');

    if (!element || !info || element.dataset.loaded === 'true') {
        return null;
    }

    element.dataset.loaded = 'true';

    const provinceLabels = [...element.querySelectorAll('.tarija-province-label')];

    const setActiveProvincia = (name) => {
        provinceLabels.forEach((label) => {
            label.classList.toggle('is-active', label.dataset.provincia === name);
        });
    };

    provinceLabels.forEach((item) => {
        item.addEventListener('mouseenter', () => setActiveProvincia(item.dataset.provincia));
        item.addEventListener('focus', () => setActiveProvincia(item.dataset.provincia));
    });

    return element;
};

window.initializeAttractionsMap = () => {
    const element = document.getElementById('google-attractions-map');
    if (!element || element.dataset.loaded === 'true') return;
    element.dataset.loaded = 'true';

    const places = window.tarijaAttractionData || [];
    const types = window.tarijaAttractionTypes || [];
    const listCards = [...document.querySelectorAll('.map-place-card')];
    const filters = [...document.querySelectorAll('[data-type-filter]')];
    const subcategories = document.getElementById('map-subcategories');
    const search = document.getElementById('map-place-search');
    const detail = document.getElementById('map-place-detail');
    const count = document.getElementById('map-result-count');
    const directionsModal = document.getElementById('directions-modal');
    const center = { lat: -21.5355, lng: -64.7296 };
    let selectedType = 'all';
    let searchTerm = '';
    let map;
    const markers = new Map();

    if (window.google?.maps) {
        map = new window.google.maps.Map(element, { center, zoom: 13, mapTypeControl: false, streetViewControl: false, fullscreenControl: true });
        places.forEach((place) => {
            const marker = new window.google.maps.Marker({ position: { lat: place.lat, lng: place.lng }, map, title: place.title, icon: { path: window.google.maps.SymbolPath.CIRCLE, fillColor: place.color, fillOpacity: 1, strokeColor: '#ffffff', strokeWeight: 3, scale: 10 } });
            marker.addListener('click', () => selectPlace(place.id));
            markers.set(place.id, { marker, setVisible: (visible) => marker.setVisible(visible), focus: () => { map.panTo(marker.getPosition()); map.setZoom(16); } });
        });
    } else {
        map = window.L.map(element, { scrollWheelZoom: true }).setView([center.lat, center.lng], 13);
        window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        places.forEach((place) => {
            const marker = window.L.circleMarker([place.lat, place.lng], { radius: 10, fillColor: place.color, fillOpacity: 1, color: '#fff', weight: 3 }).addTo(map);
            marker.on('click', () => selectPlace(place.id));
            markers.set(place.id, { marker, setVisible: (visible) => visible ? marker.addTo(map) : marker.remove(), focus: () => map.setView([place.lat, place.lng], 16) });
        });
    }

    const descendantsOf = (typeId) => types.filter((type) => String(type.parentId) === String(typeId)).map((type) => String(type.id));
    const isVisible = (place) => {
        const typeMatch = selectedType === 'all' || String(place.typeId) === selectedType || String(place.parentId) === selectedType;
        const haystack = `${place.title} ${place.summary || ''} ${place.address || ''} ${place.type || ''}`.toLowerCase();
        return typeMatch && haystack.includes(searchTerm);
    };

    const renderSubcategories = () => {
        subcategories.innerHTML = '';
        if (selectedType === 'all') return;
        descendantsOf(selectedType).forEach((id) => {
            const type = types.find((item) => String(item.id) === id);
            const button = document.createElement('button');
            button.className = 'map-subfilter';
            button.innerHTML = `<i class="fa-solid ${type.icon}"></i>${type.name}`;
            button.addEventListener('click', () => { selectedType = id; applyFilters(); });
            subcategories.appendChild(button);
        });
        const selected = types.find((item) => String(item.id) === selectedType);
        if (selected?.activities) {
            const info = document.createElement('p');
            info.className = 'w-full rounded-xl bg-red-50 p-3 text-xs leading-5 text-red-950';
            info.textContent = `Qué puedes hacer: ${selected.activities}`;
            subcategories.appendChild(info);
        }
    };

    const applyFilters = () => {
        let visibleCount = 0;
        places.forEach((place) => {
            const visible = isVisible(place);
            markers.get(place.id)?.setVisible(visible);
            const card = listCards.find((item) => Number(item.dataset.placeId) === place.id);
            if (card) card.hidden = !visible;
            if (visible) visibleCount++;
        });
        count.textContent = visibleCount;
        renderSubcategories();
    };

    const escapeText = (value) => String(value || '').replace(/[&<>'"]/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[char]));
    function selectPlace(id) {
        const place = places.find((item) => item.id === Number(id));
        if (!place) return;
        markers.get(place.id)?.focus();
        detail.hidden = false;
        detail.innerHTML = `<button class="map-detail-close" type="button"><i class="fa-solid fa-xmark"></i></button><img src="${escapeText(place.image)}" alt="${escapeText(place.title)}"><div class="p-5"><small style="color:${escapeText(place.color)}">${escapeText(place.type)}</small><h2>${escapeText(place.title)}</h2><p>${escapeText(place.summary || place.description)}</p><div class="mt-3 grid gap-2 text-xs">${place.address ? `<span><i class="fa-solid fa-location-dot"></i>${escapeText(place.address)}</span>` : ''}${place.hours ? `<span><i class="fa-solid fa-clock"></i>${escapeText(place.hours)}</span>` : ''}${place.phone ? `<span><i class="fa-solid fa-phone"></i>${escapeText(place.phone)}</span>` : ''}${place.price ? `<span><i class="fa-solid fa-tag"></i>${escapeText(place.price)}</span>` : ''}</div><div class="map-detail-actions"><button class="map-directions-button" type="button"><i class="fa-solid fa-diamond-turn-right"></i> Cómo llegar</button>${place.website ? `<a href="${escapeText(place.website)}" target="_blank" rel="noopener">Sitio web <i class="fa-solid fa-arrow-up-right-from-square"></i></a>` : ''}</div></div>`;
        detail.querySelector('.map-detail-close').addEventListener('click', () => { detail.hidden = true; });
        detail.querySelector('.map-directions-button').addEventListener('click', () => openDirections(place));
        listCards.forEach((card) => card.classList.toggle('is-active', Number(card.dataset.placeId) === place.id));
    }

    function openDirections(place) {
        if (!directionsModal) return;
        const destination = `${place.lat},${place.lng}`;
        const base = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(destination);
        document.getElementById('directions-destination').textContent = place.title + (place.address ? ` · ${place.address}` : '');
        document.getElementById('directions-walking').href = base + '&travelmode=walking';
        document.getElementById('directions-driving').href = base + '&travelmode=driving';
        document.getElementById('directions-alternatives').href = base;
        directionsModal.hidden = false;
        requestAnimationFrame(() => directionsModal.classList.add('is-visible'));
    }

    const closeDirections = () => {
        if (!directionsModal) return;
        directionsModal.classList.remove('is-visible');
        setTimeout(() => { directionsModal.hidden = true; }, 200);
    };
    document.getElementById('directions-close')?.addEventListener('click', closeDirections);
    directionsModal?.addEventListener('click', (event) => { if (event.target === directionsModal) closeDirections(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && directionsModal && !directionsModal.hidden) closeDirections(); });

    filters.forEach((button) => button.addEventListener('click', () => { selectedType = button.dataset.typeFilter; filters.forEach((item) => item.classList.toggle('is-active', item === button)); applyFilters(); }));
    listCards.forEach((card) => card.addEventListener('click', () => selectPlace(card.dataset.placeId)));
    search?.addEventListener('input', () => { searchTerm = search.value.trim().toLowerCase(); applyFilters(); });
    applyFilters();
};

const initializePortal = () => {
    initializeTourismWidget();
    window.initializeSwiper('.hero-swiper', {
        effect: 'fade',
        pagination: { el: '.hero-pagination', clickable: true },
    });

    window.initializeSwiper('.destinos-swiper', {
        slidesPerView: 1,
        spaceBetween: 24,
        breakpoints: {
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 },
        },
        pagination: { el: '.destinos-pagination', clickable: true },
    });

    window.initializeTarijaGoogleMap();
    const attractionMap = document.getElementById('map-explorer');
    // Con una clave válida, Google invoca initializeAttractionsMap mediante su callback.
    // Sin clave, la vista utiliza el mapa embebido de Google definido en Blade.

    window.initializeMap('footer-map', [-21.5355, -64.7296], {
        zoom: 9,
        popup: 'Portal Turistico Tarija',
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePortal);
} else {
    initializePortal();
}

document.addEventListener('livewire:navigated', initializePortal);
