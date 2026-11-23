// Mobile carousels: card grids become swipeable scroll-snap rows with arrows + dots (or a counter when there are many cards).
// Desktop is untouched. Pages can re-count after filtering by dispatching a "car:refresh" event on the grid.
const SELECTOR = '.grid, .ways, .dgrid, .mom, .jr, .band-grid, .stats, .plc, .tgrid';
const mq = matchMedia('(max-width: 699px)');

function build(el) {
    const ui = document.createElement('div');
    ui.className = 'car-ui' + (el.closest('.diff, .cta2') ? ' on-dark' : '');
    ui.innerHTML = '<button type="button" class="car-btn" data-p aria-label="Previous">&larr;</button><div class="car-dots"></div><button type="button" class="car-btn" data-n aria-label="Next">&rarr;</button>';
    el.after(ui);
    const dots = ui.querySelector('.car-dots');
    const items = () => [...el.children].filter((c) => !c.hidden);
    let idx = 0;

    const current = () => {
        const list = items();
        if (!list.length) return 0;
        const left = el.scrollLeft;
        let best = 0;
        let d = Infinity;
        list.forEach((c, i) => {
            const diff = Math.abs(c.offsetLeft - el.offsetLeft - left - (parseFloat(getComputedStyle(el).paddingLeft) || 0));
            if (diff < d) { d = diff; best = i; }
        });
        if (Math.ceil(left + el.clientWidth) >= el.scrollWidth - 2) best = list.length - 1;
        return best;
    };
    const go = (i) => {
        const list = items();
        i = Math.max(0, Math.min(list.length - 1, i));
        el.scrollTo({ left: list[i].offsetLeft - el.offsetLeft - (parseFloat(getComputedStyle(el).paddingLeft) || 0), behavior: 'smooth' });
    };
    const paint = () => {
        const list = items();
        ui.hidden = list.length < 2;
        idx = current();
        [...dots.children].forEach((b, i) => { b.setAttribute('aria-current', i === idx ? 'true' : 'false'); });
        const counter = dots.querySelector('.car-count');
        if (counter) counter.textContent = `${idx + 1} / ${list.length}`;
        ui.querySelector('[data-p]').disabled = idx === 0;
        ui.querySelector('[data-n]').disabled = idx >= list.length - 1;
    };
    const rebuild = () => {
        const list = items();
        dots.innerHTML = '';
        if (list.length > 8) {
            dots.innerHTML = '<span class="car-count"></span>';
        } else {
            list.forEach((_, i) => {
                const b = document.createElement('button');
                b.type = 'button';
                b.setAttribute('aria-label', `Go to ${i + 1} of ${list.length}`);
                b.addEventListener('click', () => go(i));
                dots.appendChild(b);
            });
        }
        paint();
    };
    ui.querySelector('[data-p]').addEventListener('click', () => go(idx - 1));
    ui.querySelector('[data-n]').addEventListener('click', () => go(idx + 1));
    let t;
    el.addEventListener('scroll', () => { cancelAnimationFrame(t); t = requestAnimationFrame(paint); }, { passive: true });
    el.addEventListener('car:refresh', () => { el.scrollLeft = 0; rebuild(); });
    rebuild();
    return () => { ui.remove(); el.classList.remove('is-car'); };
}

export function initCarousels() {
    const live = new Map();
    const sync = () => {
        document.querySelectorAll(SELECTOR).forEach((el) => {
            if (el.closest('.mega, .mobile')) return;
            if (mq.matches && !live.has(el) && el.children.length > 1) {
                el.classList.add('is-car');
                live.set(el, () => {});
                // Build the arrows + dots when the browser is idle so first paint is not delayed.
                (window.requestIdleCallback || setTimeout)(() => { if (live.has(el)) live.set(el, build(el)); });
            } else if (!mq.matches && live.has(el)) {
                live.get(el)();
                live.delete(el);
            }
        });
    };
    sync();
    mq.addEventListener('change', sync);
}
