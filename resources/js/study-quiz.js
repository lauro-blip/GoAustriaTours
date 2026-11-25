// The real hero quiz (home page).
// Steps are server-rendered (partial pages/partials/study-quiz.blade.php); this shows one at a time, auto-advances on tap,
// loads reCAPTCHA on the contact step and submits to the real enquiry endpoint.
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => [...r.querySelectorAll(s)];
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

let recaptchaPromise = null;
export function loadRecaptcha() {
    if (!recaptchaPromise) {
        recaptchaPromise = new Promise((resolve) => {
            window.__recaptchaReady = () => resolve(window.grecaptcha);
            const sc = document.createElement('script');
            sc.src = 'https://www.google.com/recaptcha/api.js?onload=__recaptchaReady&render=explicit';
            sc.async = true;
            sc.defer = true;
            document.head.appendChild(sc);
        });
    }
    return recaptchaPromise;
}

export function initQuiz() {
    $$('[data-quiz]').forEach((box) => {
        const form = $('[data-quiz-form]', box);
        const panels = $$('[data-step]', box);
        const last = panels.length - 1;
        const back = $('[data-q-back]', box);
        const siteKey = box.dataset.siteKey;
        const capBox = $('[data-q-captcha]', box);
        const capErr = $('[data-q-captcha-err]', box);
        let cur = Number(box.dataset.start) || 0;
        let widgetId = null;

        const renderCaptcha = () => {
            if (!siteKey || widgetId !== null || !capBox) return;
            loadRecaptcha().then((g) => g.ready(() => {
                if (widgetId !== null) return;
                widgetId = g.render(capBox, { sitekey: siteKey, callback: () => { capErr.hidden = true; } });
            }));
        };
        const show = (n) => {
            cur = Math.max(0, Math.min(last, n));
            panels.forEach((p, i) => { p.hidden = i !== cur; });
            back.hidden = cur === 0;
            if (cur === last) renderCaptcha();
        };
        $$('input[type=radio]', box).forEach((r) => r.addEventListener('change', () => setTimeout(() => show(cur + 1), reduce ? 0 : 260)));
        $$('[data-q-select]', box).forEach((sel) => sel.addEventListener('change', () => sel.value && show(cur + 1)));
        back.addEventListener('click', () => show(cur - 1));
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (!form.checkValidity()) { form.reportValidity(); return; }
            const g = window.grecaptcha;
            if (siteKey && (!g || widgetId === null || !g.getResponse(widgetId))) { capErr.hidden = false; return; }
            $('.q-submit', box).disabled = true;
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({ event: 'enquiry_submit', form_name: 'hero-quiz' });
            form.submit();
        });
        show(cur);
        box.classList.add('q-ready');
    });
}
