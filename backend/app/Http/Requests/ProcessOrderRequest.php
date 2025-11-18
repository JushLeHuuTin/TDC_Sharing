<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProcessOrderRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được ủy quyền thực hiện request này không.
     * Chỉ người dùng đã đăng nhập mới được phép tạo đơn hàng.
     */
    public function authorize(): bool
    {
        return auth::check();
    }

    /**
     * Lấy các quy tắc validation áp dụng cho request.
     * Giả định người dùng truyền lên thông tin địa chỉ và ID giao dịch/thanh toán.
     */
    public function rules(): array
    {
        return [
            // Thông tin nhận hàng (Giả định lấy từ form checkout)
            'delivery_address_id' => 'required|integer|exists:addresses,id',
            
            // Thông tin thanh toán
            'payment_method' => 'required|string|in:cash,vnpay,momo', // Ví dụ
            'transaction_id' => 'nullable|string|max:255',
            
            // Mã giảm giá (nếu có)
            'voucher_code' => 'nullable|string|exists:vouchers,code',
        ];
    }

    /**
     * Tùy chỉnh thông báo lỗi.
     */
    public function messages(): array
    {
        return [
            'delivery_address_id.required' => 'Vui lòng chọn địa chỉ giao hàng.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'transaction_id.required' => 'Không tìm thấy thông tin giao dịch.',
        ];
    }
}