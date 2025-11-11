import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            // Ensure full page reloads when backend or Blade files change inside Docker
            refresh: [
                'resources/views/**/*.blade.php',
                'routes/**/*.php',
                'app/Livewire/**/*.php',
                'app/Http/**/*.php',
                'app/Providers/**/*.php',
                'config/**/*.php',
                'resources/lang/**/*.php',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors: true,
        // Improve reliability of file watching on mounted volumes (Docker Desktop/WSL)
        watch: {
            usePolling: true,
            interval: 300,
        },
        // Help browsers connect to Vite's HMR from the host machine
        hmr: {
            host: 'localhost',
            port: 5173,
            protocol: 'ws',
        },
    },
});
