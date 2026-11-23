// Home page behaviour (plain JS): mobile menu, destination filter + 6-per-page pager, counters, review slider, reveal on scroll.
import '../css/home.css';
import { initQuiz } from './study-quiz';
import { initQuoteModal } from './study-modal';
import { initCarousels } from './carousel.js';
import { initInlineForms } from './inline-forms';

document.documentElement.classList.add('tp3-js');
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

// header: transparent over the hero, solid once scrolled
const hdr = $('.t3 .hdr');
const onScroll = () => hdr.classList.toggle('solid', scrollY > 40);
requestAnimationFrame(onScroll); // reading scrollY forces layout, so not during script evaluation
addEventListener('scroll', onScroll, { passive: true });

const mobile = $('.t3 .mobile');
$$('[data-open-menu]').forEach((b) => b.addEventListener('click', () => mobile.classList.add('open')));
$$('[data-close-menu]').forEach((b) => b.addEventListener('click', () => mobile.classList.remove('open')));

// tours: filter by destination, 6 per page, numbered pager
$$('[data-tours]').forEach((sec) => {
    const cells = $$('[data-grid] .cell', sec);
    const pager = $('[data-pager]', sec);
    const count = $('[data-count]', sec);
    const grid = $('[data-grid]', sec);
    const mqm = matchMedia('(max-width: 699px)');
    const perPage = () => 6;
    let dest = '';
    let page = 1;
    const render = () => {
        const list = cells.filter((c) => !dest || c.dataset.dest === dest);
        const per = perPage();
        const pages = Math.max(1, Math.ceil(list.length / per));
        page = Math.min(page, pages);
        cells.forEach((c) => { c.hidden = true; });
        list.slice((page - 1) * per, page * per).forEach((c) => { c.hidden = false; });
        const from = list.length ? (page - 1) * per + 1 : 0;
        count.textContent = `Showing ${from}–${Math.min(page * per, list.length)} of ${list.length} tours`;
        pager.innerHTML = '';
        pager.hidden = pages < 2;
        const mk = (label, p, opts = {}) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.textContent = label;
            if (opts.aria) b.setAttribute('aria-label', opts.aria);
            if (opts.disabled) b.disabled = true;
            if (opts.current) b.setAttribute('aria-current', 'true');
            b.addEventListener('click', () => { page = p; render(); });
            pager.appendChild(b);
        };
        mk('←', page - 1, { aria: 'Previous page', disabled: page === 1 });
        for (let n = 1; n <= pages; n++) mk(String(n), n, { current: n === page });
        mk('→', page + 1, { aria: 'Next page', disabled: page === pages });
        grid.dispatchEvent(new Event('car:refresh'));
    };
    mqm.addEventListener('change', () => { page = 1; render(); });
    const select = $('[data-dest-select]', sec);
    const setDest = (value) => {
        dest = value;
        page = 1;
        $$('.chip', sec).forEach((c) => c.classList.toggle('on', c.dataset.dest === value));
        if (select) select.value = value;
        render();
    };
    $$('.chip', sec).forEach((chip) => chip.addEventListener('click', () => setDest(chip.dataset.dest)));
    select?.addEventListener('change', () => setDest(select.value));
    render();
});

// review slider
$$('[data-reviews]').forEach((box) => {
    const track = $('.rtrack', box);
    const step = () => (track.firstElementChild ? track.firstElementChild.getBoundingClientRect().width + (parseFloat(getComputedStyle(track).columnGap) || 0) : 300);
    $('[data-prev]', box)?.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
    $('[data-next]', box)?.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
    const count = $('[data-count]', box);
    if (count) {
        const pad = (n) => String(n).padStart(2, '0');
        const total = track.children.length;
        track.addEventListener('scroll', () => {
            const i = Math.min(total, Math.round(track.scrollLeft / Math.max(1, step())) + 1);
            count.textContent = `${pad(i)} / ${pad(total)}`;
        }, { passive: true });
    }
});

// Hero slides 2 and 3 load after the page is ready so they never compete with the first paint.
addEventListener('load', () => $$('.hero-bg img[data-src]').forEach((im) => { im.srcset = im.dataset.srcset; im.src = im.dataset.src; }), { once: true });

initQuiz();
initQuoteModal();
initInlineForms();
initCarousels();

// counters + reveal
const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
        if (!e.isIntersecting) return;
        const el = e.target;
        io.unobserve(el);
        if (el.dataset.n) {
            const end = Number(el.dataset.n);
            if (reduce) { el.textContent = end; return; }
            const t0 = performance.now();
            const tick = (t) => {
                const p = Math.min(1, (t - t0) / 1400);
                el.textContent = Math.round(end * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        } else {
            el.classList.add('in');
        }
    });
}, { threshold: 0.12 });
$$('.t3 .rv, .t3 [data-n]').forEach((el) => io.observe(el));
