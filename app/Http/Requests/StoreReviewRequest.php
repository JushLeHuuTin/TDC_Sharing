<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,product_id'],
            'rating'     => ['required', 'integer', 'between:1,5'],
            'comment'    => [
                'nullable',
                'string',
                'max:300', // <-- đúng theo DB bạn yêu cầu
            
            ],
        ];
    }
    // public function messages()
    // {
    //     return [
    //         'product_id.required' => 'Không chọn',
    //         'product_id.integer'  => 'Không chọn',
    //         'product_id.exists'   => 'Sản phẩm không tồn tại',

    //         'rating.required'     => 'Không chọn',
    //         'rating.integer'      => 'Vui lòng chọn số sao hợp lệ (1-5).',
    //         'rating.between'      => 'Vui lòng chọn số sao hợp lệ (1-5).',

    //         'comment.max'         => 'Nội dung đánh giá không vượt quá 300 ký tự.',
    //     ];
    // }
    // protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    // {
    //     $errors = $validator->errors()->all();

    //     // Nếu bạn dùng authorize() = auth()->check() và muốn tách lỗi auth,
    //     // controller sẽ trả 401; ở đây chỉ xử lý validation (422).
    //     throw new ValidationException($validator, response()->json([
    //         'success'    => false,
    //         'error_type' => 'validation',
    //         'messages'   => $errors,
    //     ], 422));
    // }
}
