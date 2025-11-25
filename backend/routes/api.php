<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Api\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\orderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\MomoCallbackController;

Route::get('/products/create', [ProductController::class, function () {
    return view('page.products.create');
}])->name('product.create');
// 1.4.TIN display products list
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/my', [ProductController::class, 'getMyProduct']);
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
// 3.5 Checkout online
Route::post('/payment/momo-callback', [OrderController::class, 'handleMomoCallback']);
Route::post('/payment/momo', [CheckoutController::class, 'momoPay']);
Route::post('/momo/ipn', [MomoCallbackController::class, 'handleIpn'])->name('checkout.momo_ipn');
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'getProfile'])->name('user.profile');
    // 3.1 Dong add product to cart
    Route::post('/cart/add', [CartController::class, 'addItem'])->name('cart.add');
    // 3.2 Dong delete product from cart
    Route::delete('/cart/item/{cartItem}', [CartController::class, 'deleteItem']);
    // Dùng route::patch hoặc route::put cho hành động cập nhật
    Route::put('/cart/item/{cartItem}/toggle', [CartController::class, 'toggleItemSelection'])
        ->name('cart.toggle');
    Route::put('/cart/item/{cartItem}', [CartController::class, 'updateItemQuantity'])
        ->name('cart.update_quantity');
    // 3.3 Dong display cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::delete('/cart', [CartController::class, 'destroy']);
    // 3.4 Dong creat order
    Route::post('/orders', [OrderController::class, 'store']);
    // Route::post('/checkout/validate-voucher', [VoucherController::class, 'validateVoucher']);
    Route::get('/user/addresses', [AddressController::class, 'index']);
    // Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/momo/ipn', [MomoCallbackController::class, 'handleIpn']);
    // Route::post('/orders', [OrderController::class, 'store']);
    // 3.6 Dong add voucher
    Route::post('/vouchers', [VoucherController::class, 'store']);
    // 3.7 Dong display list vouchers
    Route::get('/vouchers', [VoucherController::class, 'index']);
    // 3.8 Dong update voucher
    Route::put('/vouchers/{id}', [VoucherController::class, 'update']);
    // 3.9 Dong delete voucher
    Route::delete('/vouchers/{voucher}', [VoucherController::class, 'destroy'])
        ->middleware('can:delete,voucher'); // 'voucher' là tham số model binding
    // 3.10 Dong add promotion
    Route::post('/promotions', [PromotionController::class, 'store']);
    // 3.11 Dong display list promotions
    Route::get('/vouchers/{id}', [VoucherController::class, 'show']);
    // Route::get('/orders/{id}', [OrderController::class, 'show']); 
    Route::get('/checkout', [CheckoutController::class, 'index']);
    // API Lấy thông tin voucher (trước khi sửa)
    Route::get('/vouchers/{id}', [VoucherController::class, 'show']);

    // API CẬP NHẬT voucher (sử dụng phương thức PUT/PATCH theo chuẩn RESTful)
    Route::put('/vouchers/{id}', [VoucherController::class, 'update']);



    Route::get('/user/products/counts', [ProductController::class, 'getMyProductStatusCounts']);

    // Route::get('/promotions/student', [PromotionController::class, 'studentPromotions']);

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
    Route::get('/categories/{id}/attributes', [CategoryController::class, 'getAttributes']);
    // 1.13.TIN display favorites product
    Route::get('/favorites', [FavoriteController::class, 'index']);
    // Group route for seller
    Route::prefix('seller')->name('seller.')->group(function () {
        //10,12.HANH display filler orders
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        //11.HANH show detail orders
        Route::get('/orders/{order}', [SellerOrderController::class, 'show'])->name('orders.show');
        //13.HANH approve order
        Route::put('/orders/{order}/approve', [SellerOrderController::class, 'approve'])->name('orders.approve');
        // Endpoint để từ chối (reject) đơn hàng
        Route::put('/orders/{order}/reject', [SellerOrderController::class, 'reject'])->name('orders.reject');

        // Các route khác của seller sẽ được thêm vào đây sau
    });
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
