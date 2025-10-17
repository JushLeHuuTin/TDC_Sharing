<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Kiểm tra quyền Admin.
     */
    public function authorize(): bool
    {
        // Kiểm tra xem người dùng đã đăng nhập và có vai trò là 'admin' hay không
        return $this->user() && $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // user_ids phải là một mảng và có ít nhất 1 phần tử
            'user_ids'   => 'required|array|min:1',
            // Mỗi user_id trong mảng phải tồn tại trong bảng 'users'
            'user_ids.*' => 'required|integer|exists:users,id',
            // Sửa 'object' thành 'type'
            'type'       => ['required', 'string', Rule::in(['Thông tin', 'Khuyến mãi', 'Cảnh báo'])],
            'content'    => 'required|string|max:255',
        ];
    }

    /**
     * Get the custom validation messages.
     */
    public function messages(): array
    {
        return [
            'user_ids.required' => 'Vui lòng chọn ít nhất một người nhận.',
            'user_ids.array'    => 'Định dạng người nhận không hợp lệ.',
            'user_ids.*.exists' => 'Một hoặc nhiều người nhận không tồn tại trong hệ thống.',
            // Sửa 'object' thành 'type'
            'type.required'     => 'Vui lòng chọn loại thông báo.',
            'type.in'           => 'Loại thông báo không hợp lệ.',
            'content.required'  => 'Vui lòng nhập nội dung thông báo.',
            'content.max'       => 'Nội dung thông báo không được vượt quá 255 ký tự.',
        ];
    }
    
    /**
     * Handle a failed validation attempt.
     * Tùy chỉnh response lỗi trả về dạng JSON.
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

