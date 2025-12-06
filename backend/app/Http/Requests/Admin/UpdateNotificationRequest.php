<?php

namespace App\Http\Requests\Admin; // Namespace đúng là Admin

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule; // Đảm bảo đã import Rule

class UpdateNotificationRequest extends FormRequest // Tên class đúng
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Phân quyền đã được xử lý bởi Policy
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // CẬP NHẬT QUAN TRỌNG: Đảm bảo các giá trị Rule::in khớp với DB
            'type'    => ['sometimes', 'required', 'string', Rule::in(['order', 'promotion', 'system', 'message'])], // <-- ĐÃ SỬA
            'content' => ['sometimes', 'required', 'string', 'max:255'],
            'is_read' => ['sometimes', 'boolean'], // Cho phép cập nhật trạng thái đọc
        ];
        // 'sometimes' nghĩa là chỉ validate nếu trường đó được gửi lên
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'content' => trim(strip_tags($this->input('content', '')))
        ]);
    }
     /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Vui lòng chọn loại thông báo.',
            'type.in' => 'Loại thông báo được chọn không hợp lệ. Các loại hợp lệ: order, promotion, system, message.',
            'content.required' => 'Vui lòng nhập nội dung thông báo.',
            'content.max' => 'Nội dung thông báo không vượt quá 255 ký tự.',
            'is_read.boolean' => 'Trạng thái đọc phải là true hoặc false.',
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