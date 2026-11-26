{{-- Injected ONLY into the admin live preview (Admin\PagePreviewController), never into public pages.
     Sections are wrapped in [data-blk] (blocks/_all when $editing), plain-text fields carry [data-edit="field.path"].
     Talks to the editor (resources/js/page-builder.js) with postMessage: select / edit / edit-end / ready, and listens for select. --}}
<style>
    [data-blk] > * { transition: outline-color .15s; }
    [data-blk]:hover > * { outline: 2px dashed #dba12c; outline-offset: -2px; cursor: pointer; }
    [data-blk].pv-sel > * { outline: 3px solid #dba12c !important; outline-offset: -3px; }
    [data-edit] { cursor: text !important; }
    [data-edit]:hover { outline: 1px dashed rgba(15, 59, 89, .7); outline-offset: 3px; }
    [data-edit][contenteditable] { outline: 2px solid #0f3b59 !important; outline-offset: 3px; background: rgba(255, 244, 214, .55); }
    #pv-tag { position: fixed; z-index: 99999; pointer-events: none; display: none; padding: 4px 9px; background: #dba12c; color: #0f3b59; font: 600 11px/1.2 system-ui, sans-serif; letter-spacing: .06em; text-transform: uppercase; border-radius: 0 0 4px 0; }
    .consult { display: none !important; }
</style>
<div id="pv-tag"></div>
<script>
(() => {
    const labels = @json($labels);
    const tag = document.getElementById('pv-tag');
    const send = (m) => parent.postMessage(Object.assign({ pv: 1 }, m), '*');
    const box = (blk) => { const el = blk.firstElementChild; return el ? el.getBoundingClientRect() : null; };
    let hover = null;
    const place = () => {
        if (!hover) { tag.style.display = 'none'; return; }
        const r = box(hover);
        if (!r) return;
        tag.textContent = labels[hover.dataset.blkType] || hover.dataset.blkType;
        tag.style.display = 'block';
        tag.style.left = Math.max(0, r.left) + 'px';
        tag.style.top = Math.max(0, r.top) + 'px';
    };
    document.addEventListener('mouseover', (e) => { hover = e.target.closest('[data-blk]'); place(); });
    document.addEventListener('scroll', place, { passive: true });

    // Inline editing of plain-text fields.
    const stopEdit = (el) => {
        if (!el.hasAttribute('contenteditable')) return;
        el.removeAttribute('contenteditable');
        send({ type: 'edit-end', i: +el.closest('[data-blk]').dataset.blk, field: el.dataset.edit, value: el.textContent.replace(/s+/g, " ").trim() });
    };
    document.addEventListener('click', (e) => {
        const edit = e.target.closest('[data-edit]');
        const blk = e.target.closest('[data-blk]');
        if (edit && edit.hasAttribute('contenteditable')) return; // let the caret move
        // Nothing on the preview navigates or submits.
        if (e.target.closest('a, button, summary, label, input, select, textarea, form')) e.preventDefault();
        e.stopPropagation();
        if (edit && blk) {
            edit.setAttribute('contenteditable', 'plaintext-only');
            if (edit.contentEditable !== 'plaintext-only') edit.setAttribute('contenteditable', 'true');
            edit.focus();
            edit.addEventListener('blur', () => stopEdit(edit), { once: true });
        }
        if (blk) send({ type: 'select', i: +blk.dataset.blk });
    }, true);
    document.addEventListener('input', (e) => {
        const el = e.target.closest('[data-edit][contenteditable]');
        if (el) send({ type: 'edit', i: +el.closest('[data-blk]').dataset.blk, field: el.dataset.edit, value: el.textContent.replace(/s+/g, " ").trim() });
    });
    document.addEventListener('keydown', (e) => {
        const el = e.target.closest && e.target.closest('[data-edit][contenteditable]');
        if (el && (e.key === 'Enter' || e.key === 'Escape')) { e.preventDefault(); el.blur(); }
    });
    document.addEventListener('submit', (e) => e.preventDefault(), true);

    window.addEventListener('message', (e) => {
        const m = e.data || {};
        if (m.type !== 'select') return;
        document.querySelectorAll('.pv-sel').forEach((x) => x.classList.remove('pv-sel'));
        const blk = document.querySelector('[data-blk="' + m.i + '"]');
        if (!blk) return;
        blk.classList.add('pv-sel');
        if (m.scroll && blk.firstElementChild) blk.firstElementChild.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
    // Reveal-on-scroll sections must be visible straight away in the editor.
    document.querySelectorAll('.rv').forEach((el) => el.classList.add('in'));
    send({ type: 'ready', height: document.documentElement.scrollHeight });
})();
</script>
