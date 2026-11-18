// Curated packages pagination: swaps the grid via ajax. Without JS the links fall back to /?curated_page=N#curated.
export function initCuratedPackages() {
    const box = document.getElementById('curated-list');
    if (!box) return;
    const endpoint = box.dataset.endpoint;
    let busy = false;

    box.addEventListener('click', async (e) => {
        const link = e.target.closest('a[data-page]');
        if (!link || e.metaKey || e.ctrlKey || e.shiftKey || busy) return;
        e.preventDefault();
        busy = true;
        const page = link.dataset.page;
        const top = document.getElementById('curated');
        box.setAttribute('aria-busy', 'true');
        box.classList.add('opacity-50', 'transition-opacity');
        try {
            const res = await fetch(`${endpoint}?curated_page=${encodeURIComponent(page)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error(res.status);
            box.innerHTML = await res.text();
            history.replaceState(null, '', page === '1' ? '#curated' : `?curated_page=${page}#curated`);
            top?.scrollIntoView({ behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' });
        } catch (err) {
            window.location.href = link.href; // graceful fallback: normal page load
            return;
        } finally {
            box.removeAttribute('aria-busy');
            box.classList.remove('opacity-50');
            busy = false;
        }
    });
}
