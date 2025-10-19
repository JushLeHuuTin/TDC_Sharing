<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Http\Request;

Route::post('/products', [ProductController::class, 'store']);
Route::get('products/{product}/reviews', [ReviewController::class, 'index']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
});
   