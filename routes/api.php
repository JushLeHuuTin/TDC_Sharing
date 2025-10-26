<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use Illuminate\Http\Request;


Route::post('/products', [ProductController::class, 'store']);

// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);

    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    // Thêm group route cho các chức năng của Seller
    Route::prefix('seller')->name('seller.')->group(function () {
        //10,12. Hien thi,loc don hang
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        //11 chi tiet don hang
        Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
        // Endpoint để duyệt (approve) đơn hàng
        //13 duyet don hang
        Route::put('/orders/{order}/approve', [SellerOrderController::class, 'approve'])->name('orders.approve');

        // Endpoint để từ chối (reject) đơn hàng
        Route::put('/orders/{order}/reject', [SellerOrderController::class, 'reject'])->name('orders.reject');

        // Các route khác của seller sẽ được thêm vào đây sau
    });
    // Group route cho các chức năng của Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');

        Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');

        Route::put('/notifications/{notification}', [AdminNotificationController::class, 'update'])->name('notifications.update');

        Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    });
});
