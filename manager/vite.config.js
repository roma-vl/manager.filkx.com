import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import symfony from 'vite-plugin-symfony'
import path from 'path'

export default defineConfig({
    plugins: [
        vue(),
        symfony()
    ],
    optimizeDeps: {
        include: ['vue3-toastify'],
    },
    root: 'resources',
    base: '/build/',
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
    },
    build: {
        manifest: true,
        outDir: '../public/build',
        emptyOutDir: true,
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, 'resources/js/app.js'),
                style: path.resolve(__dirname, 'resources/css/app.css'),
            },
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    css: {
        postcss: path.resolve(__dirname, 'postcss.config.cjs'),
    },
})
