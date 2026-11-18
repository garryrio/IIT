const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

let leafletPromise;
function loadLeaflet() {
    leafletPromise ??= Promise.all([import('leaflet'), import('leaflet/dist/leaflet.css')]).then(([L]) => L.default ?? L);
    return leafletPromise;
}

/**
 * Peta interaktif (Leaflet + OpenStreetMap), dimuat malas saat terlihat.
 * config: { center:[lat,lng], zoom, markers:[{layer,category,categoryLabel,color,lat,lng,title,text,url,image}], layers:{key:label}, picker:false }
 */
export default (config) => ({
    status: 'idle', // idle | loading | ready | error
    activeLayers: Object.keys(config.layers || {}),
    activeCategory: 'all',
    map: null,
    groups: {},
    count: (config.markers || []).length,

    init() {
        const start = () => this.load();
        if (!('IntersectionObserver' in window)) return start();
        const io = new IntersectionObserver((entries) => {
            if (entries.some((e) => e.isIntersecting)) {
                io.disconnect();
                start();
            }
        }, { rootMargin: '200px' });
        io.observe(this.$el);
        window.addEventListener('potential-filter', (e) => this.filterCategory(e.detail));
    },

    async load() {
        this.status = 'loading';
        try {
            const L = await loadLeaflet();
            const el = this.$refs.map;
            this.map = L.map(el, { scrollWheelZoom: false, zoomControl: true, attributionControl: true })
                .setView(config.center, config.zoom || 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">Kontributor OpenStreetMap</a>',
            }).addTo(this.map);
            this.map.on('focus', () => this.map.scrollWheelZoom.enable());
            this.map.on('blur', () => this.map.scrollWheelZoom.disable());

            const bounds = [];
            for (const m of config.markers || []) {
                const icon = L.divIcon({
                    className: '',
                    html: `<div class="village-pin" style="background:${esc(m.color)}"><span></span></div>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 30],
                    popupAnchor: [0, -28],
                });
                const marker = L.marker([m.lat, m.lng], { icon, title: m.title, alt: m.title, keyboard: true });
                marker.bindPopup(`
                    <div style="max-width:220px">
                      ${m.image ? `<img src="${esc(m.image)}" alt="" style="width:100%;height:96px;object-fit:cover;border-radius:10px;margin-bottom:8px">` : ''}
                      <div style="font-size:12px;font-weight:700;color:${esc(m.color)};text-transform:uppercase;letter-spacing:.04em">${esc(m.categoryLabel)}</div>
                      <div style="font-weight:700;font-size:15px;margin:2px 0 4px">${esc(m.title)}</div>
                      ${m.text ? `<div style="color:#4a524d">${esc(m.text)}</div>` : ''}
                      ${m.url ? `<a class="village-popup-link" href="${esc(m.url)}">Lihat detail →</a>` : ''}
                    </div>`);
                marker._meta = m;
                const key = m.layer || 'default';
                (this.groups[key] ??= L.layerGroup().addTo(this.map)).addLayer(marker);
                bounds.push([m.lat, m.lng]);
            }
            if (config.center && config.showCenter) {
                L.circle(config.center, { radius: 60, color: '#1f4d2b', fillColor: '#e9b23a', fillOpacity: 0.5, weight: 2 })
                    .addTo(this.map).bindPopup(`<strong>${esc(config.centerLabel || 'Kantor Desa')}</strong>`);
                bounds.push(config.center);
            }
            if (bounds.length > 1 && config.fit !== false) this.map.fitBounds(bounds, { padding: [36, 36], maxZoom: 16 });
            this.status = 'ready';
            setTimeout(() => this.map.invalidateSize(), 150);
        } catch (e) {
            console.error(e);
            this.status = 'error';
        }
    },

    toggleLayer(key) {
        if (!this.map) return;
        const i = this.activeLayers.indexOf(key);
        if (i >= 0) {
            this.activeLayers.splice(i, 1);
            this.groups[key] && this.map.removeLayer(this.groups[key]);
        } else {
            this.activeLayers.push(key);
            this.groups[key] && this.map.addLayer(this.groups[key]);
        }
    },

    layerCount(key) {
        return (config.markers || []).filter((m) => m.layer === key).length;
    },

    filterCategory(category) {
        this.activeCategory = category || 'all';
        if (!this.map) return;
        Object.values(this.groups).forEach((g) => {
            g.eachLayer((marker) => {
                const show = this.activeCategory === 'all' || marker._meta.category === this.activeCategory;
                marker.setOpacity(show ? 1 : 0.15);
                if (show) marker.setZIndexOffset(500);
                else marker.setZIndexOffset(0);
            });
        });
    },

    focusMarker(lat, lng) {
        if (!this.map) return;
        this.map.flyTo([lat, lng], 17, { duration: 0.8 });
        Object.values(this.groups).forEach((g) => g.eachLayer((m) => {
            const p = m.getLatLng();
            if (Math.abs(p.lat - lat) < 1e-7 && Math.abs(p.lng - lng) < 1e-7) setTimeout(() => m.openPopup(), 850);
        }));
        this.$refs.map.scrollIntoView({ behavior: 'smooth', block: 'center' });
    },
});
