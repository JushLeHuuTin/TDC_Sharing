<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException; // <-- Import class này
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request; // <-- Import class này
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    // ...

    public function register(): void
    {;
        $this->reportable(function (Throwable $e) {
            //
        });

        // Xử lý lỗi 404 (Not Found) cho API
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài nguyên bạn yêu cầu không tồn tại.'
                ], 404);
            }
        });

        // THÊM ĐOẠN CODE NÀY ĐỂ XỬ LÝ LỖI 403 (PHÂN QUYỀN)
        $this->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Bạn không có quyền thực hiện hành động này.'
                ], 403); // 403 Forbidden
            }
        });
    }
}