


/** @type {import('vite').UserConfig} */
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
// Sửa lỗi __dirname: Import các hàm cần thiết
import path from 'path';
import { fileURLToPath } from 'url';

// Tính toán __dirname tương đương trong môi trường ES Module
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      // Alias cơ bản để trỏ @ đến thư mục src
      '@': path.resolve(__dirname, 'src'),
    },
  },
  // Thiết lập CSS/PostCSS trực tiếp trong Vite (sử dụng import() bất đồng bộ)
});