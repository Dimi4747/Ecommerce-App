import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['src/app.jsx'],
            buildDirectory: 'dist',
            refresh: true,
        }),
        react(),
    ],
    
    server: {
        port: 5173,
        strictPort: true,
        cors: true,
        origin: 'http://localhost:5173',
    },
    
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './src'),
        },
    },
});
