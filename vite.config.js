import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js',
                // Listed so the layout's font preloads (Vite::asset) resolve in the manifest.
                'node_modules/@fontsource/cormorant-garamond/files/cormorant-garamond-latin-500-normal.woff2',
                'node_modules/@fontsource-variable/dm-sans/files/dm-sans-latin-wght-normal.woff2',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
