/**
 * Alpine components for the hero quiz and every enquiry form.
 * reCAPTCHA v2 is only loaded when a form scrolls into view (or the modal opens),
 * and every widget is rendered explicitly so several can live on one page.
 */

let recaptchaPromise = null;

export function loadRecaptcha() {
    if (!recaptchaPromise) {
        recaptchaPromise = new Promise((resolve) => {
            window.__recaptchaReady = () => resolve(window.grecaptcha);
            const s = document.createElement('script');
            s.src = 'https://www.google.com/recaptcha/api.js?onload=__recaptchaReady&render=explicit';
            s.async = true;
            s.defer = true;
            document.head.appendChild(s);
        });
    }
    return recaptchaPromise;
}

export function enquiryForm({ formName, siteKey }) {
    return {
        widgetId: null,
        captchaError: false,
        submitting: false,

        init() {
            if (!siteKey) return;
            const render = () => {
                if (this.widgetId !== null) return;
                loadRecaptcha().then((g) => {
                    g.ready(() => {
                        if (this.widgetId !== null) return;
                        this.widgetId = g.render(this.$refs.captcha, {
                            sitekey: siteKey,
                            callback: () => { this.captchaError = false; },
                        });
                    });
                });
            };
            // Render once the form is actually visible (also covers modal + later quiz steps).
            const io = new IntersectionObserver((entries) => {
                if (entries.some((e) => e.isIntersecting)) {
                    render();
                    io.disconnect();
                }
            }, { rootMargin: '200px' });
            io.observe(this.$el);
        },

        submit(event) {
            const form = event.target;
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            const g = window.grecaptcha;
            if (siteKey && (!g || this.widgetId === null || !g.getResponse(this.widgetId))) {
                this.captchaError = true;
                return;
            }
            this.submitting = true;
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ event: 'enquiry_submit', form_name: formName });
            form.submit();
        },
    };
}

export function quiz({ steps, icons = {}, formName, initial = {}, startAtContact = false }) {
    return {
        steps,
        icons,
        step: startAtContact ? steps.length : 0,
        answers: initial || {},
        formName,

        get total() { return this.steps.length + 1; },        // +1 for the contact step
        get isContact() { return this.step >= this.steps.length; },
        get current() { return this.steps[this.step] ?? null; },
        picked: null,   // option just tapped: shows the selected state briefly before auto-advancing

        // Auto-next: highlight the tapped option, then move on after a short beat (instant for reduced motion).
        choose(value) {
            if (this.picked !== null) return;   // ignore double taps while advancing
            const key = this.current.key;
            this.answers[key] = value;
            this.picked = value;
            const wait = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 260;
            setTimeout(() => { this.picked = null; if (this.current && this.current.key === key) this.next(); }, wait);
        },
        next() {
            if (this.current && this.current.type === 'dropdown' && !this.answers[this.current.key]) return;
            if (this.step < this.steps.length) this.step++;
        },
        back() { if (this.step > 0) this.step--; },

        labelFor(stepDef) {
            const v = this.answers[stepDef.key];
            const opt = stepDef.options.find((o) => o.value === v);
            return opt ? opt.label : '';
        },
    };
}
