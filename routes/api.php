<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReviewController;
Route::post('/products', [ProductController::class, 'store']);
use Illuminate\Http\Request;
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/reviews', [ReviewController::class, 'store']);

});
       
