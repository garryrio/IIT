import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import 'trix';
import 'trix/dist/trix.css';
import villageChart from './components/chart';

Alpine.plugin(focus);
Alpine.plugin(collapse);
Alpine.data('villageChart', villageChart);

// Nonaktifkan lampiran file di Trix (gambar dikelola melalui field gambar terpisah)
document.addEventListener('trix-file-accept', (e) => e.preventDefault());

Alpine.data('adminShell', () => ({
    nav: false,
    confirm: { message: '', form: null },
    init() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.dataset.confirm && !form.dataset.confirmed) {
                e.preventDefault();
                this.confirm = { message: form.dataset.confirm, form };
                this.$refs.confirmDialog.showModal();
            }
        });
    },
    accept() {
        const form = this.confirm.form;
        this.$refs.confirmDialog.close();
        if (form) {
            form.dataset.confirmed = '1';
            form.requestSubmit();
        }
    },
}));

/** Field gambar: pratinjau + perkecil otomatis di browser (maks 1920px, WebP/JPEG) sebelum diunggah. */
Alpine.data('imageField', (current = null, maxKb = 2048) => ({
    preview: current,
    removed: false,
    busy: false,
    error: '',
    async onChange(e) {
        const input = e.target;
        const file = input.files[0];
        this.error = '';
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            this.error = 'Format harus JPG, PNG, atau WebP.';
            input.value = '';
            return;
        }
        this.busy = true;
        try {
            const resized = await resize(file, 1920, 0.82);
            const out = resized && resized.size < file.size ? resized : file;
            if (out.size > maxKb * 1024) {
                this.error = `Ukuran gambar masih di atas ${Math.round(maxKb / 1024)} MB setelah diperkecil.`;
                input.value = '';
                return;
            }
            const dt = new DataTransfer();
            dt.items.add(out);
            input.files = dt.files;
            this.preview = URL.createObjectURL(out);
            this.removed = false;
        } finally {
            this.busy = false;
        }
    },
    remove() {
        this.preview = null;
        this.removed = true;
        this.$refs.file.value = '';
    },
}));

async function resize(file, max, quality) {
    try {
        const bitmap = await createImageBitmap(file);
        const scale = Math.min(1, max / Math.max(bitmap.width, bitmap.height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        const type = 'image/webp';
        const blob = await new Promise((r) => canvas.toBlob(r, type, quality));
        if (!blob) return null;
        return new File([blob], file.name.replace(/\.\w+$/, '') + '.webp', { type });
    } catch {
        return null;
    }
}

/** Pemilih koordinat: klik/geser penanda pada peta untuk mengisi latitude & longitude. */
Alpine.data('mapPicker', (lat, lng, center) => ({
    lat: lat ?? '',
    lng: lng ?? '',
    map: null,
    marker: null,
    failed: false,
    async init() {
        try {
            const [{ default: L }] = await Promise.all([import('leaflet'), import('leaflet/dist/leaflet.css')]);
            const start = this.lat && this.lng ? [Number(this.lat), Number(this.lng)] : center;
            this.map = L.map(this.$refs.map, { scrollWheelZoom: false }).setView(start, 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; Kontributor OpenStreetMap',
            }).addTo(this.map);
            const icon = L.divIcon({ className: '', html: '<div class="village-pin" style="background:#c46a16"><span></span></div>', iconSize: [30, 30], iconAnchor: [15, 30] });
            this.marker = L.marker(start, { draggable: true, icon });
            if (this.lat && this.lng) this.marker.addTo(this.map);
            this.marker.on('dragend', () => this.set(this.marker.getLatLng()));
            this.map.on('click', (e) => this.set(e.latlng));
            this.$watch('lat', () => this.sync());
            this.$watch('lng', () => this.sync());
        } catch {
            this.failed = true;
        }
    },
    set(p) {
        this.lat = p.lat.toFixed(7);
        this.lng = p.lng.toFixed(7);
    },
    sync() {
        if (!this.map) return;
        const la = Number(this.lat);
        const ln = Number(this.lng);
        if (this.lat !== '' && this.lng !== '' && Number.isFinite(la) && Number.isFinite(ln)) {
            this.marker.setLatLng([la, ln]).addTo(this.map);
        } else {
            this.marker.remove();
        }
    },
    clear() {
        this.lat = this.lng = '';
    },
}));

window.Alpine = Alpine;
Alpine.start();
