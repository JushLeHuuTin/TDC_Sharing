<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\VoucherController;

Route::post('/products', [ProductController::class, 'store']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {

    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/cart/add', [CartController::class, 'addItem'])->name('cart.add');
    Route::post('/vouchers', [VoucherController::class, 'store']);
});
