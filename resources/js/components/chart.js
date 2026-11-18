let chartPromise;
const loadChart = () => (chartPromise ??= import('chart.js/auto').then((m) => m.default));

const PALETTE = ['#1f4d2b', '#e9b23a', '#5f9e57', '#c46a16', '#1f6f8b', '#a9cf6d', '#7a4e9a', '#b5452f', '#8fbb89', '#4a524d'];
const fmt = (v) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(v);

/**
 * Grafik Chart.js yang dimuat saat terlihat.
 * config: { type: bar|horizontal|donut|line|stacked, labels:[], datasets:[{label,data,color?,colors?}], unit }
 */
export default (config) => ({
    ready: false,
    failed: false,
    init() {
        const io = new IntersectionObserver(async (entries) => {
            if (!entries.some((e) => e.isIntersecting)) return;
            io.disconnect();
            try {
                await this.render();
                this.ready = true;
            } catch (e) {
                console.error(e);
                this.failed = true;
            }
        }, { rootMargin: '150px' });
        io.observe(this.$el);
    },
    async render() {
        const Chart = await loadChart();
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const t = config.type;
        const isDonut = t === 'donut';
        const datasets = config.datasets.map((d, i) => ({
            label: d.label,
            data: d.data,
            backgroundColor: isDonut ? (d.colors || PALETTE) : (d.color || (t === 'line' ? '#1f4d2b' : (config.datasets.length > 1 ? PALETTE[i % PALETTE.length] : '#2f6b3a'))),
            borderColor: t === 'line' ? (d.color || '#1f4d2b') : isDonut ? '#fffdf8' : 'transparent',
            borderWidth: isDonut ? 3 : t === 'line' ? 3 : 0,
            borderRadius: isDonut ? 0 : 8,
            pointBackgroundColor: '#e9b23a',
            pointRadius: t === 'line' ? 5 : 0,
            tension: 0.35,
            fill: false,
            maxBarThickness: 46,
        }));
        new Chart(this.$refs.canvas, {
            type: isDonut ? 'doughnut' : t === 'line' ? 'line' : 'bar',
            data: { labels: config.labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: reduce ? false : { duration: 900 },
                indexAxis: t === 'horizontal' ? 'y' : 'x',
                cutout: isDonut ? '62%' : undefined,
                plugins: {
                    legend: { display: isDonut || config.datasets.length > 1, position: 'bottom', labels: { boxWidth: 12, boxHeight: 12, padding: 14, font: { family: 'Plus Jakarta Sans', size: 13 } } },
                    tooltip: {
                        callbacks: { label: (c) => ` ${c.dataset.label && config.datasets.length > 1 ? c.dataset.label + ': ' : (isDonut ? c.label + ': ' : '')}${fmt(c.raw)} ${config.unit || ''}` },
                    },
                },
                scales: isDonut ? {} : {
                    // Hanya sumbu nilai yang diberi formatter angka; sumbu kategori memakai label bawaan.
                    x: { stacked: t === 'stacked', grid: { display: t === 'horizontal' }, ticks: { font: { family: 'Plus Jakarta Sans' }, ...(t === 'horizontal' ? { callback: (v) => fmt(v) } : {}) } },
                    y: { stacked: t === 'stacked', beginAtZero: t !== 'line', grid: { color: '#ece6d8', display: t !== 'horizontal' }, ticks: { font: { family: 'Plus Jakarta Sans' }, ...(t !== 'horizontal' ? { callback: (v) => fmt(v) } : {}) } },
                },
            },
        });
    },
});
