<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Kiểm tra chế độ bảo trì
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Nạp Composer Autoload
require __DIR__.'/../vendor/autoload.php';

// Khởi động ứng dụng Laravel
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());