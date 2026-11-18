import '@fontsource-variable/dm-sans';
import '@fontsource/cormorant-garamond/500.css';
import '@fontsource/cormorant-garamond/500-italic.css';
import '@fontsource/cormorant-garamond/600.css';
import '@fontsource/cormorant-garamond/600-italic.css';
import '@fontsource/cormorant-garamond/700.css';
import '@fontsource/cormorant-garamond/700-italic.css';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { quiz, enquiryForm, loadRecaptcha } from './forms';
import { initCuratedPackages } from './curated';
import { initReveal } from './reveal';

window.Alpine = Alpine;
Alpine.plugin(focus);
window.loadRecaptcha = loadRecaptcha;

Alpine.data('quiz', quiz);
Alpine.data('enquiryForm', enquiryForm);

// Global modal state: the enquiry modal is opened from the top bar and tour cards.
Alpine.store('modal', {
    open: false,
    context: {},
    show(context = {}) {
        this.context = context;
        this.open = true;
    },
    hide() { this.open = false; },
});

Alpine.start();
initCuratedPackages();
initReveal();
