<?php

return [
    /*
     * URL của Vite Dev Server (chỉ sử dụng trong môi trường phát triển)
     */
    'dev_server' => env('VITE_SERVER_URL', 'http://127.0.0.1:5173'), 

    /*
     * Đường dẫn đến các tệp đã được biên dịch (manifest.json).
     * Sửa đổi để trỏ đến thư mục con .vite/
     */
    'build_path' => 'build/.vite', 
];