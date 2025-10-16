<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Models\Category;

Route::get('/products', [ProductController::class, 'index']);
Route::get('/featured-products', [ProductController::class, 'featured']); 
Route::get('/products/search', [ProductController::class, 'search']);
Route::get('/categories/{category:slug}/products', [CategoryController::class, 'showProducts']);
Route::get('/products/{product:slug}', [ProductController::class, 'show']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::put('/products/{product}', [ProductController::class, 'update']);

    Route::post('/categories', [CategoryController::class, 'store']);
    
});