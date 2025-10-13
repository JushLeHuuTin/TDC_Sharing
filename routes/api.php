<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
Route::post('/products', [ProductController::class, 'store']);
// Các route yêu cầu phải đăng nhập thì cho vào group này
Route::middleware('auth:sanctum')->group(function () {
    
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    
    Route::put('/products/{product}', [ProductController::class, 'update']);
});