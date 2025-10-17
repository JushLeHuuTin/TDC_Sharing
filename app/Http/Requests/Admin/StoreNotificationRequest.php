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
     * Kiểm tra quyền Admin ngay tại đây.
     */
    public function authorize(): bool
    {
        // Chỉ cho phép user có role là 'admin' thực hiện request này
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
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'required|integer|exists:users,id', // Đảm bảo mỗi user_id đều tồn tại
            'object'     => ['required', 'string', Rule::in(['Thông tin', 'Khuyến mãi', 'Cảnh báo'])],
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
            'user_ids.array' => 'Định dạng người nhận không hợp lệ.',
            'user_ids.*.exists' => 'Một trong số người nhận không tồn tại trong hệ thống.',
            'object.required' => 'Vui lòng chọn loại thông báo.',
            'object.in' => 'Loại thông báo không hợp lệ.',
            'content.required' => 'Vui lòng nhập nội dung thông báo.',
            'content.max' => 'Nội dung thông báo không được vượt quá 255 ký tự.',
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