// Admin > Pages editor (Alpine component "pageBuilder"). Blocks are defined server-side in App\Support\Blocks; this keeps them
// in order, edits their fields, picks photos (library, bundled site photos or a fresh upload) and posts everything as blocks_json.
let seq = 0;
const uid = () => `b${Date.now().toString(36)}${(seq++).toString(36)}`;
const clone = (o) => JSON.parse(JSON.stringify(o));

export default (cfg) => ({
    types: cfg.types,
    blanks: cfg.blanks,
    library: cfg.library || [],
    site: cfg.site || [],
    blocks: [],
    headerImg: { ref: cfg.header || '', alt: '' },
    open: {},
    dirty: false,
    home: !!cfg.home,
    insertAt: null, // null = add at the end; a number = add below that section
    addable: Object.entries(cfg.types).filter(([, t]) => t.addable),
    picker: { open: false, tab: 'library', target: null, file: null, alt: '', busy: false, error: '', q: '' },

    // Visual editor: live preview of the unsaved page (Admin\PagePreviewController), device sizes, selection, undo/redo.
    previewUrl: cfg.previewUrl || null,
    preview: false,
    device: 'desktop',
    devices: { desktop: 1280, tablet: 820, mobile: 390 },
    scale: 1,
    frameH: 800,
    loading: false,
    selected: null,
    inlineEditing: false,
    past: [],
    future: [],
    restoring: false,

    init() {
        this.blocks = (cfg.blocks || []).filter((b) => this.types[b.type]).map((b) => this.normalize(b));
        if (this.blocks.length === 1) this.open[this.blocks[0].uid] = true;
        try { this.preview = this.previewUrl && localStorage.getItem('pb-preview') !== '0' && window.innerWidth >= 1280; } catch (e) { this.preview = false; }
        this.past = [this.snap()];
        this.$nextTick(() => {
            this.$watch('blocks', () => { this.changed(); }, { deep: true });
            this.$watch('headerImg', () => { this.changed(); }, { deep: true });
            this.$watch('preview', (v) => { try { localStorage.setItem('pb-preview', v ? '1' : '0'); } catch (e) { /* private mode */ } if (v) this.$nextTick(() => { this.fit(); this.refresh(true); }); });
            this.$watch('device', () => this.$nextTick(() => this.fit()));
            if (this.preview) { this.fit(); this.refresh(true); }
        });
        window.addEventListener('resize', () => this.fit());
        window.addEventListener('message', (e) => this.onFrameMessage(e));
        window.addEventListener('keydown', (e) => this.onKey(e));
        window.addEventListener('beforeunload', (e) => {
            if (!this.dirty) return;
            e.preventDefault();
            e.returnValue = '';
        });
    },

    // ---- change tracking: dirty flag, undo history, preview refresh ----
    changed() {
        this.dirty = true;
        if (this.restoring) return;
        clearTimeout(this._rec);
        this._rec = setTimeout(() => this.record(), 400);
        if (!this.inlineEditing) this.refresh();
    },
    snap() {
        return JSON.stringify({ blocks: this.blocks.map(({ type, data, hidden }) => ({ type, data, hidden })), header: this.headerImg });
    },
    record() {
        const s = this.snap();
        if (this.past[this.past.length - 1] === s) return;
        this.past.push(s);
        if (this.past.length > 100) this.past.shift();
        this.future = [];
    },
    apply(s) {
        const d = JSON.parse(s);
        const openIdx = this.blocks.map((b, i) => (this.open[b.uid] ? i : -1)).filter((i) => i >= 0);
        this.restoring = true;
        // Fresh ids so every field (rich text included) re-renders with the restored content.
        this.blocks = d.blocks.map((b) => this.normalize(b));
        this.headerImg = d.header;
        this.open = {};
        openIdx.forEach((i) => { if (this.blocks[i]) this.open[this.blocks[i].uid] = true; });
        this.$nextTick(() => { this.restoring = false; this.refresh(true); });
    },
    undo() {
        clearTimeout(this._rec);
        this.record();
        if (this.past.length < 2) return;
        this.future.push(this.past.pop());
        this.apply(this.past[this.past.length - 1]);
    },
    redo() {
        if (!this.future.length) return;
        const s = this.future.pop();
        this.past.push(s);
        this.apply(s);
    },
    onKey(e) {
        const mod = e.ctrlKey || e.metaKey;
        if (!mod) return;
        const t = e.target;
        // Text fields keep their own native undo.
        if (t.closest && t.closest('input, textarea, select, trix-editor, [contenteditable]')) return;
        const k = e.key.toLowerCase();
        if (k === 'z' && !e.shiftKey) { e.preventDefault(); this.undo(); } else if ((k === 'z' && e.shiftKey) || k === 'y') { e.preventDefault(); this.redo(); }
    },

    // ---- live preview ----
    frame() { return this.$refs.frame; },
    fit() {
        const box = this.$refs.stage;
        if (!box) return;
        const w = this.devices[this.device];
        this.scale = Math.min(1, box.clientWidth / w);
        this.frameH = Math.round(box.clientHeight / this.scale);
    },
    formData() {
        const form = this.$root.closest('form') || this.$root;
        const fd = new FormData();
        ['title', 'eyebrow', 'intro', 'body'].forEach((n) => {
            const el = form.querySelector(`[name="${n}"]`);
            if (el) fd.append(n, el.value);
        });
        fd.append('header_image', this.headerImg.ref || '');
        fd.append('blocks_json', this.json());
        return fd;
    },
    refresh(now = false) {
        if (!this.preview || !this.previewUrl) return;
        clearTimeout(this._pv);
        this._pv = setTimeout(() => this.load(), now ? 0 : 550);
    },
    async load() {
        const f = this.frame();
        if (!f) return;
        this.loading = true;
        const seq = (this._seq = (this._seq || 0) + 1);
        try {
            const r = await fetch(this.previewUrl, { method: 'POST', body: this.formData(), headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '', Accept: 'text/html' } });
            const html = await r.text();
            if (seq !== this._seq) return; // a newer request is on its way
            const y = f.contentWindow?.scrollY || 0;
            f.onload = () => {
                try { f.contentWindow.scrollTo(0, y); } catch (e) { /* not ready */ }
                if (this.selected !== null) this.post({ type: 'select', i: this.selected });
                this.loading = false;
            };
            f.srcdoc = html;
        } catch (e) {
            this.loading = false;
        }
    },
    post(m) { try { this.frame()?.contentWindow?.postMessage(m, '*'); } catch (e) { /* frame gone */ } },
    // Selecting a section in the panel highlights (and scrolls to) it in the preview.
    focusBlock(i, scroll = true) {
        this.selected = i;
        this.post({ type: 'select', i, scroll });
    },
    setPath(obj, path, value) {
        const keys = path.split('.');
        let o = obj;
        for (let k = 0; k < keys.length - 1; k++) {
            o = o?.[/^\d+$/.test(keys[k]) ? +keys[k] : keys[k]];
            if (o === undefined) return;
        }
        o[keys[keys.length - 1]] = value;
    },
    onFrameMessage(e) {
        if (!this.frame() || e.source !== this.frame().contentWindow || !e.data?.pv) return;
        const m = e.data;
        const b = this.blocks[m.i];
        if (m.type === 'select' && b) {
            this.selected = m.i;
            this.open[b.uid] = true;
            this.$nextTick(() => document.getElementById(`blk-${b.uid}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
        }
        if ((m.type === 'edit' || m.type === 'edit-end') && b && m.field) {
            this.inlineEditing = m.type === 'edit';
            this.setPath(b.data, m.field, m.value);
            if (m.type === 'edit-end') this.refresh();
        }
    },

    // Fill in any missing fields (older saves, new field added later) and make images {ref, alt}.
    normalize(b) {
        const data = { ...clone(this.blanks[b.type].data), ...(b.data || {}) };
        Object.entries(this.types[b.type].fields).forEach(([k, f]) => {
            if (f.kind === 'image') data[k] = { ref: data[k]?.ref || '', alt: data[k]?.alt || '' };
            if (f.kind === 'items') data[k] = (Array.isArray(data[k]) ? data[k] : []).map((it) => this.normalizeItem(f.fields, it));
            if (f.kind === 'columns') data[k] = (Array.isArray(data[k]) ? data[k] : []).map((c) => ({ elements: (c?.elements || []).filter((e) => this.elements[e?.type]).map((e) => this.normalizeEl(e)) }));
        });
        return { uid: uid(), type: b.type, data, hidden: !!b.hidden };
    },

    // ---- Columns section: columns of elements (heading, text, image, button, list, quote, spacer, video) ----
    elements: cfg.elements || {},
    layoutCols: cfg.layoutCols || {},
    normalizeEl(e) {
        const def = this.elements[e.type];
        const data = { ...clone(def.blank.data), ...(e.data || {}) };
        Object.entries(def.fields).forEach(([k, f]) => {
            if (f.kind === 'image') data[k] = { ref: data[k]?.ref || '', alt: data[k]?.alt || '' };
        });
        return { _uid: uid(), _open: !!e._open, type: e.type, data };
    },
    colCount(b) { return this.layoutCols[b.data.layout] || 2; },
    // Keeps enough columns for the chosen layout (extra ones are kept, just not shown, so switching layouts loses nothing).
    syncCols(b) {
        const n = this.colCount(b);
        if (!Array.isArray(b.data.cols)) b.data.cols = [];
        while (b.data.cols.length < n) b.data.cols.push({ elements: [] });
    },
    addElement(col, type) {
        if (col.elements.length >= 12) return;
        col.elements.push(this.normalizeEl({ ...clone(this.elements[type].blank), _open: true }));
    },
    elSummary(e) {
        const d = e.data;
        return String(d.text || d.label || d.caption || d.title || d.items || d.url || (d.body || '').replace(/<[^>]+>/g, ' ') || '').trim().slice(0, 50);
    },
    isFixed(b) { return !!this.types[b.type]?.fixed; },
    isPinned(b) { return !!this.types[b.type]?.pinned; },
    canMove(i, dir) {
        const j = i + dir;
        return j >= 0 && j < this.blocks.length && !this.isPinned(this.blocks[i]) && !this.isPinned(this.blocks[j]);
    },
    toggleHidden(b) { if (!this.isPinned(b)) b.hidden = !b.hidden; },
    addBelow(i) {
        this.insertAt = i + 1;
        this.$nextTick(() => document.getElementById('add-panel')?.scrollIntoView({ behavior: 'smooth', block: 'center' }));
    },
    normalizeItem(fields, it = {}) {
        const out = {};
        Object.entries(fields).forEach(([k, f]) => {
            out[k] = f.kind === 'image' ? { ref: it[k]?.ref || '', alt: it[k]?.alt || '' } : (it[k] ?? '');
        });
        return out;
    },

    add(type) {
        const b = this.normalize(clone(this.blanks[type]));
        const f = this.types[type].fields;
        Object.entries(f).forEach(([k, def]) => {
            if (def.kind === 'items' && type === 'steps') b.data[k] = [1, 2, 3].map(() => this.normalizeItem(def.fields));
            else if (def.kind === 'items') b.data[k] = [this.normalizeItem(def.fields)];
        });
        if (type === 'columns') {
            const el = (t) => this.normalizeEl(clone(this.elements[t].blank));
            b.data.cols = [{ elements: [el('heading'), el('text'), el('button')] }, { elements: [el('image')] }];
        }
        const at = this.insertAt === null ? this.blocks.length : this.insertAt;
        this.blocks.splice(at, 0, b);
        this.insertAt = null;
        this.open[b.uid] = true;
        this.$nextTick(() => document.getElementById(`blk-${b.uid}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
    },
    move(i, dir) {
        const j = i + dir;
        if (!this.canMove(i, dir)) return;
        const [b] = this.blocks.splice(i, 1);
        this.blocks.splice(j, 0, b);
    },
    duplicate(i) {
        if (this.isFixed(this.blocks[i])) return;
        const copy = { ...clone(this.blocks[i]), uid: uid() };
        this.blocks.splice(i + 1, 0, copy);
        this.open[copy.uid] = true;
    },
    remove(i) {
        if (this.isFixed(this.blocks[i])) return;
        if (confirm(`Remove this "${this.types[this.blocks[i].type].label}" section?`)) this.blocks.splice(i, 1);
    },
    toggle(b) {
        this.open[b.uid] = !this.open[b.uid];
        if (this.open[b.uid]) this.focusBlock(this.blocks.indexOf(b));
    },
    summary(b) {
        const d = b.data;
        if (b.type === 'home_hero') return [d.h1_before, d.h1_highlight, d.h1_after, d.h1_italic].filter(Boolean).join(' ');
        if (b.type === 'columns' && !d.heading) return `${this.colCount(b)} columns`;
        const text = [d.heading, d.heading_em].filter(Boolean).join(' ') || d.title || d.eyebrow || (d.items?.[0]?.title ?? d.items?.[0]?.question ?? '') || (d.body || '').replace(/<[^>]+>/g, ' ');
        return String(text).trim().slice(0, 70) || 'No heading yet';
    },

    addItem(list, fields, max) {
        if (list.length < max) list.push(this.normalizeItem(fields));
    },
    moveItem(list, j, dir) {
        const k = j + dir;
        if (k < 0 || k >= list.length) return;
        const [it] = list.splice(j, 1);
        list.splice(k, 0, it);
    },
    removeItem(list, j) { list.splice(j, 1); },

    // Photos
    thumb(ref) {
        return (this.library.find((m) => m.ref === ref) || this.site.find((m) => m.ref === ref))?.thumb || '';
    },
    openPicker(target) {
        Object.assign(this.picker, { open: true, target, file: null, alt: '', error: '', q: '', tab: this.library.length ? 'library' : 'site' });
    },
    filtered(list) {
        const q = this.picker.q.trim().toLowerCase();
        return q ? list.filter((m) => (m.label || '').toLowerCase().includes(q)) : list;
    },
    choose(item) {
        const t = this.picker.target;
        t.ref = item.ref;
        if (!t.alt && item.alt) t.alt = item.alt;
        this.picker.open = false;
    },
    clearImage(t) { t.ref = ''; t.alt = ''; },
    async upload() {
        const p = this.picker;
        if (!p.file) { p.error = 'Choose a photo to upload.'; return; }
        if (!p.alt.trim()) { p.error = 'Describe the photo first (alt text). It helps Google and screen readers.'; return; }
        p.busy = true;
        p.error = '';
        const fd = new FormData();
        fd.append('image', p.file);
        fd.append('alt', p.alt.trim());
        try {
            const r = await fetch(cfg.uploadUrl, { method: 'POST', body: fd, headers: { Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' } });
            const j = await r.json().catch(() => ({}));
            if (!r.ok) throw new Error(j.message || Object.values(j.errors || {})[0]?.[0] || 'Upload failed.');
            this.library.unshift(j);
            this.choose(j);
        } catch (e) {
            p.error = e.message;
        } finally {
            p.busy = false;
        }
    },

    json() {
        return JSON.stringify(this.blocks.map(({ type, data, hidden }) => ({ type, data, hidden })));
    },
});
