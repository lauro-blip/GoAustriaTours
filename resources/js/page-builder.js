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

    init() {
        this.blocks = (cfg.blocks || []).filter((b) => this.types[b.type]).map((b) => this.normalize(b));
        if (this.blocks.length === 1) this.open[this.blocks[0].uid] = true;
        this.$nextTick(() => {
            this.$watch('blocks', () => { this.dirty = true; }, { deep: true });
            this.$watch('headerImg', () => { this.dirty = true; }, { deep: true });
        });
        window.addEventListener('beforeunload', (e) => {
            if (!this.dirty) return;
            e.preventDefault();
            e.returnValue = '';
        });
    },

    // Fill in any missing fields (older saves, new field added later) and make images {ref, alt}.
    normalize(b) {
        const data = { ...clone(this.blanks[b.type].data), ...(b.data || {}) };
        Object.entries(this.types[b.type].fields).forEach(([k, f]) => {
            if (f.kind === 'image') data[k] = { ref: data[k]?.ref || '', alt: data[k]?.alt || '' };
            if (f.kind === 'items') data[k] = (Array.isArray(data[k]) ? data[k] : []).map((it) => this.normalizeItem(f.fields, it));
        });
        return { uid: uid(), type: b.type, data, hidden: !!b.hidden };
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
    toggle(b) { this.open[b.uid] = !this.open[b.uid]; },
    summary(b) {
        const d = b.data;
        if (b.type === 'home_hero') return [d.h1_before, d.h1_highlight, d.h1_after, d.h1_italic].filter(Boolean).join(' ');
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
