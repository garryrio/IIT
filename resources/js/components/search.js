/** Smart Search (command palette): Ctrl+K atau "/" untuk membuka. Hasil dikelompokkan per jenis konten. */
export default (endpoint) => ({
    open: false,
    q: '',
    types: [],
    from: '',
    to: '',
    sort: 'relevance',
    showFilters: false,
    loading: false,
    results: null,
    error: false,
    active: -1,
    timer: null,
    typeOptions: {
        announcement: 'Pengumuman', event: 'Agenda', news: 'Berita', service: 'Layanan', potential: 'Potensi', facility: 'Fasilitas', gallery: 'Galeri',
    },
    init() {
        window.addEventListener('keydown', (e) => {
            const typing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName) || document.activeElement?.isContentEditable;
            if ((e.key === 'k' && (e.ctrlKey || e.metaKey)) || (e.key === '/' && !typing && !this.open)) {
                e.preventDefault();
                this.show();
            }
        });
        window.addEventListener('open-search', (e) => this.show(e.detail));
    },
    show(q = '') {
        this.open = true;
        if (q) {
            this.q = q;
            this.search();
        }
        this.$nextTick(() => this.$refs.input?.focus());
    },
    hide() {
        this.open = false;
    },
    toggleType(t) {
        const i = this.types.indexOf(t);
        i >= 0 ? this.types.splice(i, 1) : this.types.push(t);
        this.search();
    },
    get filterCount() {
        return this.types.length + (this.from ? 1 : 0) + (this.to ? 1 : 0) + (this.sort !== 'relevance' ? 1 : 0);
    },
    resetFilters() {
        this.types = [];
        this.from = this.to = '';
        this.sort = 'relevance';
        this.search();
    },
    debounced() {
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.search(), 250);
    },
    get flat() {
        return (this.results?.groups || []).flatMap((g) => g.items);
    },
    indexOf(item) {
        return this.flat.indexOf(item);
    },
    async search() {
        if (this.q.trim().length < 2 && !this.from && !this.to) {
            this.results = null;
            return;
        }
        const params = new URLSearchParams({ q: this.q, sort: this.sort });
        this.types.forEach((t) => params.append('types[]', t));
        if (this.from) params.set('from', this.from);
        if (this.to) params.set('to', this.to);
        this.loading = true;
        this.error = false;
        try {
            const res = await fetch(`${endpoint}?${params}`, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error(res.status);
            this.results = await res.json();
            this.active = this.flat.length ? 0 : -1;
        } catch {
            this.error = true;
        } finally {
            this.loading = false;
        }
    },
    move(n) {
        if (!this.flat.length) return;
        this.active = (this.active + n + this.flat.length) % this.flat.length;
        this.$nextTick(() => document.getElementById(`hasil-${this.active}`)?.scrollIntoView({ block: 'nearest' }));
    },
    go() {
        const item = this.flat[this.active];
        if (item) window.location.href = item.url;
    },
    quick(q) {
        this.q = q;
        this.search();
        this.$refs.input?.focus();
    },
});
