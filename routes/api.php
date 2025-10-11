<?php
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\Api\ProductController;
// use App\Http\Controllers\api\PaymentController;
// Route::prefix('admin')->group(function () {
//     Route::prefix('products')->group(function () {
//         Route::get('/', [ProductController::class, 'index'])->name('api.products.index');
//         Route::get('/{id}', [ProductController::class, 'show'])->name('api.products.show');
//         Route::post('/', [ProductController::class, 'store'])->name('api.products.store');
//         Route::put('/{id}', [ProductController::class, 'update'])->name('api.products.update');
//         Route::get('/search/{s}', [ProductController::class, 'find'])->name('api.products.search');
//         Route::delete('/{id}', [ProductController::class, 'destroy'])->name('api.products.destroy');
//     });
//     Route::prefix('danh-muc')->group(function (){
//         // Route::get('/',[CategoryController::class,'index'])->name('api.category.index');
//         // Route::get('/{id}',[CategoryController::class,'show'])->name('api.category.show');
//         // Route::post('/',[CategoryController::class,'store'])->name('api.category.store');
//         // Route::put('/{id}',[CategoryController::class,'update'])->name('api.category.update');
//         // Route::delete('/{id}',[CategoryController::class,'destroy'])->name('api.category.destroy');
//     });
//     // Route::apiResource('products', ProductController::class);
//     // viet tiep tuc route vao day
//     // 
//     // 
//     // 
//     // 
// });
// // Route::get('payment/visa', [PaymentController::class, 'visaCheckout'])->name('payment.visa');
// // Route::post('payment/visa/process', [PaymentController::class, 'visaProcess'])->name('payment.visa.process');



// Route::middleware('auth:sanctum')->group(function () {
//     // Lấy thuộc tính của danh mục
//     Route::get('/categories/{id}/attributes', [ProductController::class, 'getCategoryAttributes']);
    
//     // Tạo sản phẩm mới
// });
Route::post('/products', [ProductController::class, 'store']);
