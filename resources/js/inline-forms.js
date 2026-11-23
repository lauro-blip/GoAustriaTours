// Inline enquiry forms on inner pages ([data-qform]): client-side checks, reCAPTCHA (rendered when the form first comes into view), then a normal POST.
import { loadRecaptcha } from './study-quiz';

export function initInlineForms() {
    document.querySelectorAll('[data-qform]').forEach((form) => {
        const siteKey = form.dataset.siteKey;
        const box = form.querySelector('[data-qf-captcha]');
        const err = form.querySelector('[data-qf-captcha-err]');
        let widgetId = null;
        const render = () => {
            if (!siteKey || widgetId !== null || !box) return;
            loadRecaptcha().then((g) => g.ready(() => {
                if (widgetId !== null) return;
                widgetId = g.render(box, { sitekey: siteKey, callback: () => { err.hidden = true; } });
            }));
        };
        if ('IntersectionObserver' in window) {
            const io = new IntersectionObserver((entries) => { if (entries.some((e) => e.isIntersecting)) { io.disconnect(); render(); } }, { rootMargin: '300px' });
            io.observe(form);
        } else {
            render();
        }
        form.addEventListener('focusin', render, { once: true });
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!form.checkValidity()) { form.reportValidity(); return; }
            const g = window.grecaptcha;
            if (siteKey && (!g || widgetId === null || !g.getResponse(widgetId))) { err.hidden = false; return; }
            form.querySelector('.qf-submit').disabled = true;
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ event: 'enquiry_submit', form_name: form.dataset.formName });
            form.submit();
        });
        // a failed server-side submit comes back with errors: bring that form into view
        if (form.querySelector('.err:not([hidden])') && form.querySelector('em.err')) form.scrollIntoView({ block: 'center' });
    });
}
