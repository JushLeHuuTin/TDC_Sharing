<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Models\Category;

Route::post('/products', [ProductController::class, 'store']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::prefix('products')->name('reviews.')->group(function () {

    });
    Route::post('/categories', [CategoryController::class, 'store']);
    
});