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
            
            // Thông tin thanh toán
            'payment_method' => 'required|string|in:cod,cash,vnpay,momo', // Ví dụ
            // 'transaction_id' => 'required|string|max:255', // ID giao dịch từ cổng thanh toán
            
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
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'transaction_id.required' => 'Không tìm thấy thông tin giao dịch.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        // Trả về phản hồi JSON với lỗi validation (status 422 - Unprocessable Entity)
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors() // Lấy danh sách lỗi
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY) // Mã HTTP 422
        );
    }
}