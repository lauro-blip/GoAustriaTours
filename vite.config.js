import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/admin.js', 'resources/js/home.js', 'resources/css/inner.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
