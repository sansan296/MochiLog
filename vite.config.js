import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        {
            // ✅ build 後に manifest.json を public/build にコピー
            name: 'move-manifest',
            closeBundle() {
                const src = path.resolve(__dirname, 'public/build/.vite/manifest.json');
                const dest = path.resolve(__dirname, 'public/build/manifest.json');
                if (fs.existsSync(src)) {
                    fs.copyFileSync(src, dest);
                    console.log('✅ manifest.json moved to public/build/');
                } else {
                    console.error('❌ manifest.json not found at', src);
                }
            },
        },
    ],
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
        },
    },
});
