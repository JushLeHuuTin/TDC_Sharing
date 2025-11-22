<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Validation\Validator;

class ProcessOrderRequest extends FormRequest
{
 
    public function authorize(): bool
    {

        return auth::check();
    }

    public function rules(): array
    {
        return [
            'address_id' => 'required|integer|exists:addresses,id',
            'payment_method' => 'required|string|in:cod,vnpay,momo', 
            'transaction_id' => 'nullable|string|max:255',
            'voucher_code' => 'nullable|string|exists:vouchers,code',
        ];
    }

    public function messages(): array
    {
        return [
            'address_id.required' => 'Vui lòng chọn địa chỉ giao hàng.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
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
                'message' => $firstError ?? 'Đặt hàng thất bại, vui lòng thử lại.',
                'errors' => $validator->errors() 
            ], 422) // Mã HTTP 422
        );
    }
}
