const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const DAYS = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
const iso = (d) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

/** Kalender agenda bulanan (data agenda dari CMS). */
export default (events = []) => ({
    events,
    days: DAYS,
    cursor: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
    selected: iso(new Date()),
    today: iso(new Date()),
    init() {
        const upcoming = this.events.find((e) => e.date >= this.today);
        if (upcoming && !this.eventsOn(this.today).length) {
            const d = new Date(upcoming.date + 'T00:00:00');
            if (d.getMonth() === this.cursor.getMonth() && d.getFullYear() === this.cursor.getFullYear()) this.selected = upcoming.date;
        }
    },
    get title() {
        return `${MONTHS[this.cursor.getMonth()]} ${this.cursor.getFullYear()}`;
    },
    get cells() {
        const y = this.cursor.getFullYear();
        const m = this.cursor.getMonth();
        const first = new Date(y, m, 1);
        const offset = (first.getDay() + 6) % 7; // Senin sebagai hari pertama
        const total = new Date(y, m + 1, 0).getDate();
        const cells = [];
        for (let i = 0; i < offset; i++) cells.push(null);
        for (let d = 1; d <= total; d++) {
            const date = iso(new Date(y, m, d));
            cells.push({ day: d, date, events: this.eventsOn(date) });
        }
        while (cells.length % 7) cells.push(null);
        return cells;
    },
    get monthEvents() {
        const key = iso(this.cursor).slice(0, 7);
        return this.events.filter((e) => e.date.startsWith(key));
    },
    eventsOn(date) {
        return this.events.filter((e) => e.date === date);
    },
    move(n) {
        this.cursor = new Date(this.cursor.getFullYear(), this.cursor.getMonth() + n, 1);
        const first = this.monthEvents[0];
        this.selected = first ? first.date : iso(this.cursor);
    },
    goToday() {
        const now = new Date();
        this.cursor = new Date(now.getFullYear(), now.getMonth(), 1);
        this.selected = this.today;
    },
    longDate(date) {
        return new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(date + 'T00:00:00'));
    },
    cellLabel(c) {
        const n = c.events.length;
        return `${this.longDate(c.date)}${n ? `, ${n} agenda` : ', tidak ada agenda'}`;
    },
});
