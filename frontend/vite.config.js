/** @type {import('vite').UserConfig} */
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
// Sửa lỗi __dirname: Import các hàm cần thiết
import path from 'path';
import { fileURLToPath } from 'url';

// Tính toán __dirname tương đương trong môi trường ES Module
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Xác định đường dẫn tương đối từ Frontend đến Backend Public
// Giả định cấu trúc: /TDC_Sharing/backend/ và /TDC_Sharing/frontend/
const LARAVEL_PUBLIC_DIR = '../backend/public';

export default defineConfig({
    plugins: [vue()],
    
    // Cấu hình Dev Server (Khi chạy npm run dev)
    server: {
        // Dev server sẽ chạy trên cổng này (mặc định là 5173)
        // Cần thiết lập host để ngrok/expose có thể truy cập
        host: '0.0.0.0', 
        port: 5173, 
    },

    // Cấu hình Build (Khi chạy npm run build)
    build: {
        // Bắt buộc: Đặt output vào thư mục public/build của Laravel Backend
        outDir: path.resolve(__dirname, LARAVEL_PUBLIC_DIR, 'build'),
        emptyOutDir: true,
        manifest: true, // Bắt buộc để Laravel biết cách tải các tệp
    },

    resolve: {
        alias: {
            // Alias cơ bản để trỏ @ đến thư mục src
            '@': path.resolve(__dirname, 'src'),
        },
    },
});