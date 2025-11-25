<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class ConflictException extends Exception
{
    /**
     * Khởi tạo ngoại lệ xung đột trạng thái dữ liệu.
     * Mặc định sử dụng mã trạng thái 409 Conflict.
     *
     * @param string $message Thông báo lỗi cho người dùng.
     * @param int $code Mã trạng thái HTTP (mặc định 409).
     * @param \Throwable|null $previous Ngoại lệ trước đó.
     */
    public function __construct(
        $message = "Conflict in data state (e.g., out of stock or quantity limit exceeded).",
        $code = Response::HTTP_CONFLICT, // Mã 409
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
