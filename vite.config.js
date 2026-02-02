import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // Pastikan input ini mengarah ke .scss
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // --- TAMBAHKAN BAGIAN INI ---
    css: {
        preprocessorOptions: {
            scss: {
                // Ini akan membungkam peringatan dari folder node_modules (Bootstrap)
                quietDeps: true,
                // Jika masih muncul, tambahkan ini:
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
    // ----------------------------
});