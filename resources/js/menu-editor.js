// Admin > Menus (Alpine component "menuEditor"): header items + button, footer columns, footer texts and bottom links.
// Everything is posted as header_json / footer_json and cleaned server-side by App\Support\Menus.
export default (cfg) => ({
    h: cfg.header,
    f: cfg.footer,
    choices: cfg.choices,
    headerLabels: cfg.headerLabels,
    footerLabels: cfg.footerLabels,
    mega: cfg.mega,
    dirty: false,

    init() {
        this.$nextTick(() => {
            this.$watch('h', () => { this.dirty = true; }, { deep: true });
            this.$watch('f', () => { this.dirty = true; }, { deep: true });
        });
        window.addEventListener('beforeunload', (e) => {
            if (!this.dirty) return;
            e.preventDefault();
            e.returnValue = '';
        });
    },
    move(list, i, dir) {
        const j = i + dir;
        if (j < 0 || j >= list.length) return;
        const [x] = list.splice(i, 1);
        list.splice(j, 0, x);
    },
    remove(list, i) { list.splice(i, 1); },
    addHeaderLink() { this.h.items.push({ key: 'link', label: '', url: '', hidden: false }); },
    addColumn() { this.f.columns.push({ key: 'links', title: 'Useful links', hidden: false, links: [{ label: '', url: '' }] }); },
    addLink(list) { list.push({ label: '', url: '' }); },
    // Picking a page from the list fills the link (and the label when it is still empty).
    pick(target, url) {
        if (!url) return;
        target.url = url;
        if (!target.label) target.label = this.choices.find((c) => c.url === url)?.label || '';
    },
    builtinName(it) { return this.headerLabels[it.key] || ''; },
    hasMega(it) { return this.mega.includes(it.key); },
    json(x) { return JSON.stringify(x); },
});
