import { animate, inView, stagger, scroll } from 'framer-motion/dom';

const EASE = [0.22, 1, 0.36, 1];

export const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Animasi halus berbasis framer-motion (versi DOM, tanpa React):
 * - [data-reveal]       muncul saat masuk viewport
 * - [data-stagger]      anak-anak muncul berurutan
 * - [data-count]        angka statistik berjalan
 * - [data-parallax]     gambar bergeser lembut saat scroll
 * Semua dinonaktifkan bila pengguna memilih prefers-reduced-motion.
 */
export function initMotion() {
    const root = document.documentElement;
    if (prefersReducedMotion()) {
        root.classList.remove('js-motion');
        document.querySelectorAll('[data-count]').forEach((el) => (el.textContent = format(el)));
        return;
    }

    document.querySelectorAll('[data-reveal]').forEach((el) => {
        const delay = Number(el.dataset.revealDelay || 0);
        const y = el.dataset.reveal === 'fade' ? 0 : 24;
        inView(el, () => {
            animate(el, { opacity: [0, 1], y: [y, 0] }, { duration: 0.7, delay, ease: EASE });
        }, { margin: '0px 0px -10% 0px' });
    });

    document.querySelectorAll('[data-stagger]').forEach((group) => {
        const children = Array.from(group.children);
        children.forEach((c) => (c.style.opacity = '0'));
        inView(group, () => {
            animate(children, { opacity: [0, 1], y: [18, 0] }, { duration: 0.55, delay: stagger(0.07), ease: EASE });
        }, { margin: '0px 0px -10% 0px' });
    });

    document.querySelectorAll('[data-count]').forEach((el) => {
        const target = Number(el.dataset.count);
        const decimals = Number(el.dataset.decimals || 0);
        el.textContent = format(el, 0);
        inView(el, () => {
            animate(0, target, {
                duration: 1.4,
                ease: EASE,
                onUpdate: (v) => (el.textContent = format(el, v, decimals)),
            });
        });
    });

    document.querySelectorAll('[data-parallax]').forEach((el) => {
        const distance = Number(el.dataset.parallax || 40);
        scroll(animate(el, { y: [0, distance] }, { ease: 'linear' }), { target: el.parentElement, offset: ['start start', 'end start'] });
    });

    root.classList.add('motion-ready');
}

function format(el, value = Number(el.dataset.count), decimals = Number(el.dataset.decimals || 0)) {
    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(value);
}

export { animate, stagger, EASE };
