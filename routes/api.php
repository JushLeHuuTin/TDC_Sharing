<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
Route::post('/products', [ProductController::class, 'store']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'addItem'])->name('cart.add');
    // Bạn có thể thêm các route khác cho giỏ hàng ở đây (xóa, cập nhật số lượng,...)
});
});