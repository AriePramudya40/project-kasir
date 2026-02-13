import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',      // ✅ Ganti 0.0.0.0 → localhost
        port: 5173,
        cors: true,
        https: false,           // Vite dev server via HTTP (Laravel proxy ke HTTPS)
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    
});