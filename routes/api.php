<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Api\CheckoutController;

Route::post('/products', [ProductController::class, 'store']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {

    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/cart/add', [CartController::class, 'addItem'])->name('cart.add');
    Route::delete('/cart/{cartItem}', [CartController::class, 'deleteItem']);
    Route::post('/vouchers', [VoucherController::class, 'store']);
     // API tạo đơn hàng sau khi thanh toán
    Route::post('/orders', [OrderController::class, 'store']);
    
    // API xem chi tiết đơn hàng (có kiểm tra bảo mật)
    Route::get('/orders/{id}', [OrderController::class, 'show']); 

    // API Lấy thông tin voucher (trước khi sửa)
    Route::get('/vouchers/{id}', [VoucherController::class, 'show']); 
    
    // API CẬP NHẬT voucher (sử dụng phương thức PUT/PATCH theo chuẩn RESTful)
    Route::put('/vouchers/{id}', [VoucherController::class, 'update']);

    // API xem giỏ hàng
    Route::get('/cart', [CartController::class, 'index']);
    // API Lấy dữ liệu trang Checkout
    Route::get('/checkout', [CheckoutController::class, 'index']);
    
    // Ràng buộc 7: Nút Hoàn tất đặt hàng (Gọi lại API tạo đơn đã xây dựng)
    Route::post('/orders', [OrderController::class, 'store']);
    
    // API Lấy danh sách voucher
    Route::get('/vouchers', [VoucherController::class, 'index']);
});
