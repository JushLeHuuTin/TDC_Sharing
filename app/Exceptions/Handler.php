<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // <-- Import class này
use Throwable;

class Handler extends ExceptionHandler
{
    // ... các thuộc tính khác

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // THÊM ĐOẠN CODE NÀY VÀO
        $this->renderable(function (NotFoundHttpException $e, $request) {
            // Nếu request là một API call, trả về lỗi JSON
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài nguyên bạn yêu cầu không tồn tại.'
                ], 404);
            }
        });
    }
}