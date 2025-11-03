import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    chunkSizeWarningLimit: 1000, // (optional) raise limit to 1MB
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    define: {
        __VUE_PROD_DEVTOOLS__: true // should be false for production only
    },
    build: {
        rollupOptions: {
            output: {
            manualChunks: undefined, // optional tweak to control chunking
        },
    },
        cssCodeSplit: true, // ensures CSS is split and loaded with JS
    }
});
