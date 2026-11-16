/**
 * Gentle scroll reveal: each non-hero section on the home page fades up once as it scrolls into view.
 * Skipped for reduced-motion visitors and browsers without IntersectionObserver (content then simply shows).
 */
export function initReveal() {
    if (!('IntersectionObserver' in window)) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const targets = [...document.querySelectorAll('main > section:not(:first-child) > .container-page')];
    if (!targets.length) return;

    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (!e.isIntersecting) return;
            e.target.classList.add('is-in');
            io.unobserve(e.target);
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });

    targets.forEach((t) => {
        // Anything already on screen (or above it) is shown immediately: no flash on load or anchor jumps.
        if (t.getBoundingClientRect().top < window.innerHeight * 0.92) { t.dataset.reveal = ''; t.classList.add('is-in'); return; }
        t.dataset.reveal = '';
        io.observe(t);
    });
    document.documentElement.classList.add('reveal-ready');
}
