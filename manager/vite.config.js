import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [vue()],
    root: 'resources', // resources — це твоя робоча папка з кодом
    base: '/build/',
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
    },
    build: {
        outDir: '../public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: path.resolve(__dirname, 'resources/js/app.js'),
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    css: {
        postcss: path.resolve(__dirname, 'postcss.config.cjs'), // Обережно: тут шлях до postcss.config.cjs має бути *з кореня* проєкту, не з resources
    },
})
