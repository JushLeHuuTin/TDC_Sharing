<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\orderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;

use App\Models\Category;
// 1.4.TIN display products list
Route::get('/products', [ProductController::class, 'index']);
// 1.5.TIN display feature products list
Route::get('/featured-products', [ProductController::class, 'featured']); 
// 1.6.TIN search product with keywords
Route::get('/products/search', [ProductController::class, 'search']);
// 1.7.TIN display product list by category
Route::get('/categories/{category:slug}/products', [CategoryController::class, 'showProducts']);
// 1.8.TIN display detail product
Route::get('/products/{product:slug}', [ProductController::class, 'show']);
// 1.14.TIN display category for home page
Route::get('/categories/top-five', [CategoryController::class, 'topFive']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    // 1.1.TIN add product   
    Route::post('/products', [ProductController::class, 'store']);
    // 1.2.TIN update product
    Route::put('/products/{product}', [ProductController::class, 'update']);
    // 1.3.TIN delete product
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    // 1.9.TIN add category
    Route::post('/categories', [CategoryController::class, 'store']);
    // 1.10.TIN update category
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    // 1.11.TIN delete category
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    // 1.12.TIN display categories
    Route::get('/categories', [CategoryController::class, 'index']);
    // 1.13.TIN display favorites product
    Route::get('/favorites', [FavoriteController::class, 'index']);
    
    // 2.1 HANH add reviews 
    Route::post('/reviews', [ReviewController::class, 'store']);
    // 2.2.HANH display reviews
    Route::get('products/{product}/reviews', [ReviewController::class, 'index']);
    // 2.3.HANH delete reviews
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    // 2.4.HANH update reviews 
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    // action for admin
    Route::prefix('admin')->name('admin.')->group(function () {
        // 2.5.HANH display order for seller
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        // 2.6.HANH add notifications
        Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
        // 2.7.Hanh update notifictions
        Route::put('/notifications/{notification}', [AdminNotificationController::class, 'update'])->name('notifications.update');
        // 2.8.HANH display notifications admin
        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        // 2.9.HANH delete notifications
        Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
        // 2.14,15,16 Endpoint để lấy dữ liệu thống kê dashboard
        Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');
        
    });
    
});