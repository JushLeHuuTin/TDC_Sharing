<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class ConflictException extends Exception
{
    /**
     * Mã trạng thái HTTP mặc định là 409 Conflict.
     * @var int
     */
    protected $code = Response::HTTP_CONFLICT;

    /**
     * Khởi tạo ngoại lệ.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        // Gán mã HTTP 409 nếu không có mã nào được truyền vào
        parent::__construct($message, $code ?: Response::HTTP_CONFLICT, $previous);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * Phương thức này được sử dụng khi bạn ném ngoại lệ này và 
     * nó không được bắt bởi Controller.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(Request $request)
    {
        // Khi ngoại lệ này được ném ra, Laravel sẽ tự động gọi phương thức này
        // và trả về phản hồi JSON với mã 409 (Conflict).
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->code);
    }
}