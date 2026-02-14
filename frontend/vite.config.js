import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  server: {
    port: 5173,
    proxy: {
      '/api': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
      '/sanctum': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
      '/login': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
      '/register': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
      '/logout': { target: 'http://127.0.0.1:8000', changeOrigin: true, secure: false },
    },
  },
});
