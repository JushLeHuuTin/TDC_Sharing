<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

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

            'address_id' => 'required|integer|exists:addresses,id',
            'user_id' => 'required|integer|exists:addresses,id',
            // Thông tin thanh toán
            'payment_method' => 'required|string|in:cod,cash,vnpay,momo',
            // 'total_amount' => 'required|numeric|min:0',
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
            // 1. Thông tin giao hàng (Lấy từ bảng addresses)
            'address_id.required' => 'Chưa có địa chỉ giao hàng. Vui lòng thêm mới.',
            'address_id.integer'  => 'ID địa chỉ không hợp lệ.',
            'address_id.exists'   => 'Địa chỉ giao hàng không tồn tại.',

            // 3. Phương thức thanh toán
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in'       => 'Phương thức thanh toán không hợp lệ.', 

            // // 5. Số tiền (Giảm giá)
            // 'total_amount.required' => 'Lỗi: Giá sản phẩm tạm tính không hợp lệ. Vui lòng liên hệ shop.',
            // 'total_amount.numeric'  => 'Lỗi: Giá sản phẩm tạm tính không hợp lệ. Vui lòng liên hệ shop.',

            // 9. Xác thực người dùng (user_id)
            'user_id.required' => 'Lỗi xác thực. Vui lòng đăng nhập để đặt hàng.',
            'user_id.integer'  => 'Lỗi xác thực người dùng.',
            'user_id.exists'   => 'Tài khoản người dùng không tồn tại.',

            // Mã giảm giá
            'voucher_code.string' => 'Mã voucher không hợp lệ.',
            'voucher_code.exists' => 'Không thể áp dụng voucher/khuyến mãi. Vui lòng kiểm tra lại.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // Lấy thông báo lỗi đầu tiên (hoặc tất cả các lỗi)
        $firstError = $validator->errors()->first();
        
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                // Trả về lỗi đầu tiên theo yêu cầu của bạn, hoặc dùng 'errors' để xem chi tiết
                'message' => $firstError ?? 'Đặt hàng thất bại, vui lòng thử lại.',
                'errors' => $validator->errors() 
            ], 422) // Mã HTTP 422
        );
    }
}
