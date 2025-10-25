<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'integer', 'exists:users,id'],
            // SỬA LỖI: Thay thế các giá trị trong Rule::in bằng các giá trị ENUM thực tế từ database
            'type'       => ['required', 'string', Rule::in(['info', 'promotion', 'warning'])], // <-- SỬA Ở ĐÂY
            'content'    => ['required', 'string', 'max:255'],
        ];
    }

     /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        // ... (phần messages giữ nguyên hoặc cập nhật nếu cần) ...
        return [
            'user_ids.required' => 'Vui lòng chọn người nhận.',
            'user_ids.array' => 'Danh sách người nhận phải là một mảng.',
            'user_ids.min' => 'Vui lòng chọn ít nhất một người nhận.',
            'user_ids.*.integer' => 'Mỗi ID người nhận phải là số nguyên.',
            'user_ids.*.exists' => 'Một hoặc nhiều ID người nhận không tồn tại.',
            'type.required' => 'Vui lòng chọn loại thông báo.',
            'type.in' => 'Loại thông báo được chọn không hợp lệ.',
            'content.required' => 'Vui lòng nhập nội dung thông báo.',
            'content.max' => 'Nội dung thông báo không vượt quá 255 ký tự.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        // ... (phần failedValidation giữ nguyên) ...
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.',
            'errors'  => $validator->errors()
        ], 422));
    }
}