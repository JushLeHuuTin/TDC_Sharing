<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class FilterSellerOrdersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Phân quyền sẽ được xử lý trong Controller/Policy
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status'    => ['nullable', 'string', Rule::in(['processing', 'delivering', 'delivered', 'cancelled'])],
            'from_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'to_date'   => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:from_date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Vui lòng chọn trạng thái hợp lệ.',
            'from_date.date_format' => 'Ngày bắt đầu phải có định dạng Y-m-d.',
            'to_date.date_format' => 'Ngày kết thúc phải có định dạng Y-m-d.',
            'to_date.after_or_equal' => 'Ngày kết thúc không được nhỏ hơn ngày bắt đầu.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.',
            'errors'  => $validator->errors()
        ], 422));
    }
}
