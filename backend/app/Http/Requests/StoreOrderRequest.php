<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class StoreOrderRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được phép thực hiện request này không.
     * Bắt buộc phải là người dùng đã đăng nhập (đã kiểm tra qua middleware).
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Định nghĩa các quy tắc validation cho request.
     */
    public function rules(): array
    {
        return [
            // Ràng buộc 1: Địa chỉ đã chọn
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
            
            // Ràng buộc 3: Phương thức thanh toán đã chọn
            'payment_method_id' => ['required', 'integer', 'exists:payment_methods,id'],
            
            // Ràng buộc 5: Mã giảm giá (Nếu có)
            'voucher_code' => ['nullable', 'string', 'max:20', 'exists:vouchers,code'],
            
            // Thông tin vận chuyển theo từng shop (Dữ liệu phức tạp hơn)
            // Vì một đơn hàng có thể có nhiều shop, frontend sẽ gửi mảng này
            'shipping_details' => ['required', 'array'],
            'shipping_details.*.seller_id' => ['required', 'integer', 'exists:users,id'],
            'shipping_details.*.shipping_method_id' => ['required', 'integer', 'exists:shipping_methods,id'],
            // Có thể thêm kiểm tra tồn kho, nhưng logic này nên nằm trong Controller Transaction.
        ];
    }
    
    /**
     * Định nghĩa các thông báo lỗi tùy chỉnh.
     */
    public function messages(): array
    {
        return [
            'address_id.required' => 'Vui lòng chọn địa chỉ giao hàng.',
            'address_id.exists' => 'Địa chỉ giao hàng không hợp lệ.',
            'payment_method_id.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method_id.exists' => 'Phương thức thanh toán không hợp lệ.',
            'voucher_code.exists' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.',
            // ... (Thêm các thông báo khác)
        ];
    }
}