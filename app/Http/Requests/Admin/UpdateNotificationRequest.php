<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Kiểm tra quyền Admin.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Lưu ý: Không cho phép thay đổi người nhận (user_id) khi chỉnh sửa.
        // Chỉnh sửa chỉ áp dụng cho nội dung của thông báo.
        return [
            'object'  => ['required', 'string', Rule::in(['Thông tin', 'Khuyến mãi', 'Cảnh báo'])],
            'content' => 'required|string|max:255',
            'is_read' => 'required|boolean',
        ];
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            'object.required'  => 'Vui lòng chọn loại thông báo.',
            'object.in'        => 'Loại thông báo không hợp lệ.',
            'content.required' => 'Vui lòng nhập nội dung thông báo.',
            'content.max'      => 'Nội dung thông báo không được vượt quá 255 ký tự.',
            'is_read.required' => 'Vui lòng chọn trạng thái đọc.',
            'is_read.boolean'  => 'Trạng thái đọc không hợp lệ.',
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