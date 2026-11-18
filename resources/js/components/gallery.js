/** Galeri: filter kategori + lightbox dialog yang dapat dipakai dengan keyboard. */
export default (items = [], openId = 0) => ({
    items,
    category: 'all',
    index: null,
    get filtered() {
        return this.category === 'all' ? this.items : this.items.filter((i) => i.category === this.category);
    },
    get current() {
        return this.index === null ? null : this.filtered[this.index];
    },
    init() {
        if (openId) {
            const i = this.filtered.findIndex((x) => x.id === openId);
            if (i >= 0) this.$nextTick(() => this.show(i));
        }
    },
    visible(item) {
        return this.category === 'all' || item.category === this.category;
    },
    showById(id) {
        this.show(this.filtered.findIndex((x) => x.id === id));
    },
    show(i) {
        this.index = i;
        this.$refs.dialog.showModal();
        document.body.style.overflow = 'hidden';
    },
    hide() {
        this.$refs.dialog.close();
    },
    onClose() {
        this.index = null;
        document.body.style.overflow = '';
    },
    step(n) {
        if (this.index === null) return;
        this.index = (this.index + n + this.filtered.length) % this.filtered.length;
    },
});
