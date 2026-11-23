// Quote popup for the design studies. Any [data-quote] link opens it (the link's href stays as the no-JS fallback).
import { loadRecaptcha } from './study-quiz';

export function initQuoteModal() {
    const dlg = document.querySelector('[data-qm]');
    if (!dlg || typeof dlg.showModal !== 'function') return;
    const form = dlg.querySelector('[data-qm-form]');
    const siteKey = dlg.dataset.siteKey;
    const capBox = dlg.querySelector('[data-qm-captcha]');
    const capErr = dlg.querySelector('[data-qm-captcha-err]');
    const pkgId = dlg.querySelector('[data-qm-package-id]');
    const pkgFor = dlg.querySelector('[data-qm-for]');
    let widgetId = null;

    const renderCaptcha = () => {
        if (!siteKey || widgetId !== null || !capBox) return;
        loadRecaptcha().then((g) => g.ready(() => {
            if (widgetId !== null) return;
            widgetId = g.render(capBox, { sitekey: siteKey, callback: () => { capErr.hidden = true; } });
        }));
    };
    const notes = dlg.querySelector('textarea[name=special_request]');
    const open = (pkg, interest) => {
        if (interest && notes && !notes.value.trim()) notes.value = `I am interested in ${interest}.`;
        if (pkg) {
            pkgId.value = pkg.id;
            pkgFor.textContent = 'Enquiring about: ';
            const b = document.createElement('b');
            b.textContent = pkg.title;
            pkgFor.append(b);
            pkgFor.hidden = false;
        } else if (!dlg.hasAttribute('data-open')) {
            pkgId.value = '';
            pkgFor.hidden = true;
        }
        if (!dlg.open) dlg.showModal();
        renderCaptcha();
    };

    document.addEventListener('click', (e) => {
        const a = e.target.closest('[data-quote]');
        if (!a || e.metaKey || e.ctrlKey || e.shiftKey) return;
        e.preventDefault();
        document.querySelector('.t3 .mobile')?.classList.remove('open');
        open(a.dataset.packageId ? { id: a.dataset.packageId, title: a.dataset.package } : null, a.dataset.interest);
    });
    dlg.querySelector('[data-qm-close]').addEventListener('click', () => dlg.close());
    dlg.addEventListener('click', (e) => { if (e.target === dlg) dlg.close(); });   // click on the backdrop

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!form.checkValidity()) { form.reportValidity(); return; }
        const g = window.grecaptcha;
        if (siteKey && (!g || widgetId === null || !g.getResponse(widgetId))) { capErr.hidden = false; return; }
        dlg.querySelector('.qm-submit').disabled = true;
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'enquiry_submit', form_name: 'header-modal' });
        form.submit();
    });

    // a failed server-side submit reopens the popup with the errors
    if (dlg.hasAttribute('data-open')) open(null);
}
