/** Formulir publik: stempel waktu anti-spam, pratinjau foto, ambil lokasi perangkat (opsional). */
export const publicForm = () => ({
    ts: Math.floor(Date.now() / 1000),
    preview: null,
    fileError: '',
    locating: false,
    lat: '',
    lng: '',
    geoMessage: '',
    sending: false,
    onFile(e, maxKb = 2048) {
        const file = e.target.files[0];
        this.fileError = '';
        this.preview = null;
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            this.fileError = 'Format foto harus JPG, PNG, atau WebP.';
            e.target.value = '';
            return;
        }
        if (file.size > maxKb * 1024) {
            this.fileError = `Ukuran foto maksimal ${Math.round(maxKb / 1024)} MB.`;
            e.target.value = '';
            return;
        }
        this.preview = URL.createObjectURL(file);
    },
    locate() {
        if (!navigator.geolocation) {
            this.geoMessage = 'Perangkat tidak mendukung fitur lokasi.';
            return;
        }
        this.locating = true;
        this.geoMessage = '';
        navigator.geolocation.getCurrentPosition(
            (p) => {
                this.lat = p.coords.latitude.toFixed(7);
                this.lng = p.coords.longitude.toFixed(7);
                this.locating = false;
                this.geoMessage = 'Lokasi berhasil ditambahkan.';
            },
            () => {
                this.locating = false;
                this.geoMessage = 'Lokasi tidak dapat diambil. Anda dapat mengisi nama lokasi secara manual.';
            },
            { enableHighAccuracy: true, timeout: 10000 },
        );
    },
    clearLocation() {
        this.lat = this.lng = '';
        this.geoMessage = '';
    },
});
