<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StoreCartRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được ủy quyền thực hiện yêu cầu này không.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Nhận các quy tắc xác thực áp dụng cho yêu cầu.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            // Bắt buộc, phải là số nguyên, và phải tồn tại trong bảng products
            'product_id' => 'required|integer|exists:products,id', 
            
            // Tùy chọn, phải là số nguyên, giá trị tối thiểu là 1
            'quantity' => 'sometimes|integer|min:1', 
        ];
    }

    /**
     * Tùy chỉnh thông báo lỗi (Quan trọng cho API)
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Mã sản phẩm là bắt buộc.',
            'product_id.integer' => 'Mã sản phẩm phải là số nguyên.',
            'product_id.exists' => 'Sản phẩm này không tồn tại trong hệ thống.',
            'quantity.integer' => 'Số lượng phải là số nguyên.',
            'quantity.min' => 'Số lượng tối thiểu phải là 1.',
        ];
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Lỗi xác thực dữ liệu đầu vào.',
            'errors' => $validator->errors(),
        ], 422)); // 422 Unprocessable Content
    }
}