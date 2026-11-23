import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/style.css', 'resources/js/app.js'],
            assets: ['resources/images/**', 'resources/fonts/**'],
            refresh: true,
            // The Herd site is vertrouwdag.test, not the directory name, so point the plugin at that certificate.
            // Without it the dev server silently falls back to http, which browsers block as mixed content.
            detectTls: 'vertrouwdag.test',
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
