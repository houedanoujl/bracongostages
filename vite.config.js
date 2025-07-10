import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true, // Activer le hot reload pour le développement
        }),
    ],
    server: {
        port: 5173,
        strictPort: true, // Empêche Vite de changer de port automatiquement
        host: true, // Permet l'accès depuis n'importe quelle IP
        hmr: {
            host: 'localhost',
            port: 5173,
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
    },
    optimizeDeps: {
        include: ['alpinejs'],
    },
}); 