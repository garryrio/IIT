import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';
import { initMotion } from './motion';
import villageMap from './components/map';
import villageChart from './components/chart';
import calendar from './components/calendar';
import searchPalette from './components/search';
import gallery from './components/gallery';
import catalog from './components/catalog';
import { publicForm } from './components/forms';

Alpine.plugin(focus);
Alpine.plugin(collapse);

Alpine.data('villageMap', villageMap);
Alpine.data('villageChart', villageChart);
Alpine.data('calendar', calendar);
Alpine.data('searchPalette', searchPalette);
Alpine.data('gallery', gallery);
Alpine.data('catalog', catalog);
Alpine.data('publicForm', publicForm);

Alpine.data('siteHeader', () => ({
    open: false,
    scrolled: false,
    init() {
        const onScroll = () => (this.scrolled = window.scrollY > 12);
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        window.matchMedia('(min-width: 1280px)').addEventListener('change', (e) => e.matches && (this.open = false));
    },
    toggle() {
        this.open = !this.open;
        document.body.style.overflow = this.open ? 'hidden' : '';
    },
    close() {
        this.open = false;
        document.body.style.overflow = '';
    },
}));

window.Alpine = Alpine;
Alpine.start();
initMotion();
