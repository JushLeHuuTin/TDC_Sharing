<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Không cần kiểm tra quyền ở đây, sẽ dùng Policy trong Controller
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
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:300',
        ];
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'comment' => trim(strip_tags($this->input('comment', '')))
        ]);
    }
    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Vui lòng chọn số sao đánh giá.',
            'rating.integer'  => 'Số sao phải là một số nguyên.',
            'rating.between'  => 'Vui lòng chọn số sao hợp lệ (từ 1 đến 5).',
            'comment.string'  => 'Nội dung đánh giá phải là một chuỗi ký tự.',
            'comment.max'     => 'Nội dung đánh giá không được vượt quá 300 ký tự.',
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
        ], 422)); // 422 Unprocessable Entity
    }
}