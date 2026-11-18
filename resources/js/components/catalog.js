/** Katalog potensi: pencarian & filter kategori di sisi klien, tersinkron dengan peta. */
export default (total = 0) => ({
    q: '',
    category: 'all',
    total,
    shown: total,
    init() {
        const params = new URLSearchParams(location.search);
        if (params.get('kategori')) this.category = params.get('kategori');
        if (params.get('q')) this.q = params.get('q');
        this.$watch('q', () => this.apply());
        this.$watch('category', () => this.apply());
        this.$nextTick(() => this.apply());
    },
    matches(el) {
        const text = (el.dataset.search || '').toLowerCase();
        const words = this.q.toLowerCase().trim().split(/\s+/).filter(Boolean);
        return (this.category === 'all' || el.dataset.category === this.category) && words.every((w) => text.includes(w));
    },
    apply() {
        let n = 0;
        this.$root.querySelectorAll('[data-item]').forEach((el) => {
            const ok = this.matches(el);
            el.hidden = !ok;
            if (ok) n++;
        });
        this.shown = n;
        window.dispatchEvent(new CustomEvent('potential-filter', { detail: this.category }));
        const url = new URL(location.href);
        this.category !== 'all' ? url.searchParams.set('kategori', this.category) : url.searchParams.delete('kategori');
        this.q ? url.searchParams.set('q', this.q) : url.searchParams.delete('q');
        history.replaceState(null, '', url);
    },
    reset() {
        this.q = '';
        this.category = 'all';
    },
});
