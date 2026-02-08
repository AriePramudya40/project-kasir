import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss', // Pastikan ini mengarah ke file SCSS baru
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});