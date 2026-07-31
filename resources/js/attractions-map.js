import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const initializeFreeMap = () => {
    const element = document.getElementById('google-attractions-map');
    if (!element || element.dataset.freeMapLoaded === 'true') return;
    element.dataset.freeMapLoaded = 'true';

    const places = window.tarijaAttractionData || [];
    const types = window.tarijaAttractionTypes || [];
    const cards = [...document.querySelectorAll('.map-place-card')];
    const filters = [...document.querySelectorAll('[data-type-filter]')];
    const subcategories = document.getElementById('map-subcategories');
    const search = document.getElementById('map-place-search');
    const detail = document.getElementById('map-place-detail');
    const count = document.getElementById('map-result-count');
    const modal = document.getElementById('directions-modal');
    let selectedType = 'all';
    let query = '';

    const map = L.map(element, { scrollWheelZoom: true, zoomControl: true }).setView([-21.5355, -64.7296], 11);
    element.classList.add('is-ready');
    document.getElementById('fallback-map-markers')?.classList.add('leaflet-is-ready');
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const escape = (value) => String(value || '').replace(/[&<>'"]/g, (char) => ({ '&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;' }[char]));
    const markers = new Map();

    const closeDirections = () => {
        modal?.classList.remove('is-visible');
        setTimeout(() => { if (modal) modal.hidden = true; }, 200);
    };

    const openDirections = (place) => {
        const destination = `${place.lat},${place.lng}`;
        const base = `https://www.google.com/maps/dir/?api=1&destination=${encodeURIComponent(destination)}`;
        document.getElementById('directions-destination').textContent = `${place.title}${place.address ? ` · ${place.address}` : ''}`;
        document.getElementById('directions-walking').href = `${base}&travelmode=walking`;
        document.getElementById('directions-driving').href = `${base}&travelmode=driving`;
        document.getElementById('directions-alternatives').href = base;
        modal.hidden = false;
        requestAnimationFrame(() => modal.classList.add('is-visible'));
    };

    const selectPlace = (id) => {
        const place = places.find((item) => item.id === Number(id));
        if (!place) return;
        map.flyTo([place.lat, place.lng], 15, { duration: 1.1 });
        markers.get(place.id)?.openPopup();
        detail.hidden = false;
        detail.innerHTML = `<button class="map-detail-close"><i class="fa-solid fa-xmark"></i></button><img src="${escape(place.image)}" alt="${escape(place.title)}"><div class="p-5"><small style="color:${escape(place.color)}">${escape(place.type)}</small><h2>${escape(place.title)}</h2><p>${escape(place.summary || place.description)}</p><div class="mt-3 grid gap-2 text-xs">${place.address ? `<span><i class="fa-solid fa-location-dot"></i>${escape(place.address)}</span>` : ''}${place.hours ? `<span><i class="fa-solid fa-clock"></i>${escape(place.hours)}</span>` : ''}</div><div class="map-detail-actions"><button class="map-directions-button"><i class="fa-solid fa-diamond-turn-right"></i> Cómo llegar</button></div></div>`;
        detail.querySelector('.map-detail-close').addEventListener('click', () => { detail.hidden = true; });
        detail.querySelector('.map-directions-button').addEventListener('click', () => openDirections(place));
        cards.forEach((card) => card.classList.toggle('is-active', Number(card.dataset.placeId) === place.id));
    };

    places.forEach((place) => {
        const marker = L.circleMarker([place.lat, place.lng], { radius: 11, fillColor: place.color, fillOpacity: 1, color: '#fff', weight: 3 })
            .bindPopup(`<strong>${escape(place.title)}</strong><br><small>${escape(place.type)}</small>`)
            .addTo(map);
        marker.on('click', () => selectPlace(place.id));
        markers.set(place.id, marker);
    });

    const visible = (place) => {
        const typeMatches = selectedType === 'all' || String(place.typeId) === selectedType || String(place.parentId) === selectedType;
        return typeMatches && `${place.title} ${place.summary || ''} ${place.address || ''} ${place.type || ''}`.toLowerCase().includes(query);
    };

    const renderSubcategories = () => {
        subcategories.innerHTML = '';
        if (selectedType === 'all') return;
        types.filter((type) => String(type.parentId) === selectedType).forEach((type) => {
            const button = document.createElement('button');
            button.className = 'map-subfilter';
            button.textContent = type.name;
            button.addEventListener('click', () => { selectedType = String(type.id); applyFilters(); });
            subcategories.appendChild(button);
        });
        const type = types.find((item) => String(item.id) === selectedType);
        if (type?.activities) {
            const info = document.createElement('p');
            info.className = 'w-full rounded-xl bg-red-50 p-3 text-xs leading-5 text-red-950';
            info.textContent = `Qué puedes hacer: ${type.activities}`;
            subcategories.appendChild(info);
        }
    };

    function applyFilters() {
        let total = 0;
        places.forEach((place) => {
            const show = visible(place);
            const marker = markers.get(place.id);
            if (show && !map.hasLayer(marker)) marker.addTo(map);
            if (!show && map.hasLayer(marker)) marker.remove();
            const card = cards.find((item) => Number(item.dataset.placeId) === place.id);
            if (card) card.hidden = !show;
            if (show) total++;
        });
        count.textContent = total;
        renderSubcategories();
    }

    filters.forEach((button) => button.addEventListener('click', () => {
        selectedType = button.dataset.typeFilter;
        filters.forEach((item) => item.classList.toggle('is-active', item === button));
        applyFilters();
    }));
    cards.forEach((card) => card.addEventListener('click', () => selectPlace(card.dataset.placeId)));
    search?.addEventListener('input', () => { query = search.value.trim().toLowerCase(); applyFilters(); });
    document.getElementById('directions-close')?.addEventListener('click', closeDirections);
    modal?.addEventListener('click', (event) => { if (event.target === modal) closeDirections(); });
    window.addEventListener('resize', () => map.invalidateSize());
    setTimeout(() => map.invalidateSize(), 300);
    applyFilters();
};

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeFreeMap);
else initializeFreeMap();
