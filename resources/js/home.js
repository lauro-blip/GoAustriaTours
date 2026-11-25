// Home page behaviour (plain JS): mobile menu, destination filter + 6-per-page pager, counters, review slider, reveal on scroll.
import '../css/home.css';
import { initQuiz } from './study-quiz';
import { initQuoteModal } from './study-modal';
import { initCarousels } from './carousel.js';
import { initInlineForms } from './inline-forms';
import { initPhoneFields } from './phone.js';

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

// Desktop mega menus: aria-expanded on the triggers, Escape closes the open one.
$$('.t3 .nav > li > button').forEach((btn) => {
    const li = btn.parentElement;
    btn.setAttribute('aria-haspopup', 'true');
    btn.setAttribute('aria-expanded', 'false');
    const sync = () => btn.setAttribute('aria-expanded', String(li.matches(':hover, :focus-within') && !li.classList.contains('esc')));
    li.addEventListener('mouseenter', () => { li.classList.remove('esc'); sync(); });
    li.addEventListener('mouseleave', () => { li.classList.remove('esc'); sync(); });
    li.addEventListener('focusin', sync);
    li.addEventListener('focusout', () => setTimeout(sync, 0));
    li.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        li.classList.add('esc');
        btn.focus();
        sync();
    });
});

// Mobile menu: sections are collapsed until tapped (one open at a time).
$$('.t3 .mobile .sub-t').forEach((btn) => btn.addEventListener('click', () => {
    const open = btn.getAttribute('aria-expanded') !== 'true';
    $$('.t3 .mobile .sub-t').forEach((b) => { b.setAttribute('aria-expanded', 'false'); b.parentElement.classList.remove('open'); });
    btn.setAttribute('aria-expanded', String(open));
    btn.parentElement.classList.toggle('open', open);
}));

// Package page, Places & map: hovering or focusing a place in the list highlights its dot on the map.
$$('.plx').forEach((box) => {
    const pts = $$('.plx-pt', box);
    $$('.plx-list li', box).forEach((li) => {
        const on = (v) => {
            li.classList.toggle('on', v);
            pts.forEach((p) => p.classList.toggle('on', v && p.dataset.plx === li.dataset.plx));
        };
        li.addEventListener('mouseenter', () => on(true));
        li.addEventListener('mouseleave', () => on(false));
        li.addEventListener('focusin', () => on(true));
        li.addEventListener('focusout', () => on(false));
    });
});

// Package page: itinerary / highlights / inclusions / places tabs. Without JS every panel shows, one after another.
$$('[data-ptabs]').forEach((list) => {
    const tabs = $$('[role=tab]', list);
    const panels = tabs.map((t) => document.getElementById(t.getAttribute('aria-controls')));
    const select = (i, focus) => {
        tabs.forEach((t, j) => {
            t.setAttribute('aria-selected', String(i === j));
            t.tabIndex = i === j ? 0 : -1;
            panels[j].hidden = i !== j;
        });
        if (focus) tabs[i].focus();
    };
    tabs.forEach((t, i) => {
        t.addEventListener('click', () => select(i));
        t.addEventListener('keydown', (e) => {
            const k = { ArrowRight: i + 1, ArrowLeft: i - 1, Home: 0, End: tabs.length - 1 }[e.key];
            if (k === undefined) return;
            e.preventDefault();
            select((k + tabs.length) % tabs.length, true);
        });
    });
    list.hidden = false;
    list.closest('section').classList.add('has-tabs');
    // Open the tab that holds a linked anchor (e.g. #pp-places).
    const id = decodeURIComponent(location.hash.slice(1));
    const target = id ? document.getElementById(id) : null;
    const fromHash = target ? panels.findIndex((p) => p.contains(target)) : -1;
    select(fromHash > -1 ? fromHash : 0);
});

initQuiz();
initQuoteModal();
initInlineForms();
initPhoneFields();
// Date fields open the calendar on click (Chrome/Edge only open it from the small icon otherwise).
$$('input[type=date][data-date]').forEach((i) => i.addEventListener('click', () => { try { i.showPicker?.(); } catch (e) { /* already open */ } }));
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
