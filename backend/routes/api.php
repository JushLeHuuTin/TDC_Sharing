<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Models\Category;
// 4.TIN display products list
Route::get('/products', [ProductController::class, 'index']);
// 5.TIN display feature products list
Route::get('/featured-products', [ProductController::class, 'featured']); 
// 6.TIN search product with keywords
Route::get('/products/search', [ProductController::class, 'search']);
// 7.TIN display product list by category
Route::get('/categories/{category:slug}/products', [CategoryController::class, 'showProducts']);
// 8.TIN display detail product
Route::get('/products/{product:slug}', [ProductController::class, 'show']);
// 14.TIN display category for home page
Route::get('/categories/top-five', [CategoryController::class, 'topFive']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    // 1.TIN add product   
    Route::post('/products', [ProductController::class, 'store']);
    // 2.TIN update product
    Route::put('/products/{product}', [ProductController::class, 'update']);
    // 3.TIN delete product
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    // 9.TIN add category
    Route::post('/categories', [CategoryController::class, 'store']);
    // 10.TIN update category
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    // 11.TIN delete category
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    // 12.TIN display categories
    Route::get('/categories', [CategoryController::class, 'index']);
    // 13.TIN display favorites product
    Route::get('/favorites', [FavoriteController::class, 'index']);
    
});