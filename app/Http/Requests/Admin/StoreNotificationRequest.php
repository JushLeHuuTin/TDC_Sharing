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
<<<<<<< HEAD
     * Kiểm tra quyền Admin.
     */
    public function authorize(): bool
    {
        // Kiểm tra xem người dùng đã đăng nhập và có vai trò là 'admin' hay không
        return $this->user() && $this->user()->role === 'admin';
=======
     * Logic phân quyền sẽ được xử lý bởi Policy, ở đây chỉ cần trả về true.
     */
    public function authorize(): bool
    {
        return true;
>>>>>>> hanh/f6/create-notification
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
<<<<<<< HEAD
            // user_ids phải là một mảng và có ít nhất 1 phần tử
            'user_ids'   => 'required|array|min:1',
            // Mỗi user_id trong mảng phải tồn tại trong bảng 'users'
            'user_ids.*' => 'required|integer|exists:users,id',
            // Sửa 'object' thành 'type'
            'type'       => ['required', 'string', Rule::in(['Thông tin', 'Khuyến mãi', 'Cảnh báo'])],
            'content'    => 'required|string|max:255',
=======
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'integer', 'exists:users,id'], // Mỗi user_id phải tồn tại trong bảng users
            'type'       => ['required', 'string', Rule::in(['order', 'promotion', 'system', 'message'])],
            'content'    => ['required', 'string', 'max:255'],
>>>>>>> hanh/f6/create-notification
        ];
    }

     /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
<<<<<<< HEAD
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
=======
            'user_ids.required' => 'Vui lòng chọn người nhận.',
            'type.required' => 'Vui lòng chọn loại thông báo.',
            'content.required' => 'Vui lòng nhập nội dung thông báo.',
            'content.max' => 'Nội dung thông báo không vượt quá 255 ký tự.',
        ];
    }
>>>>>>> hanh/f6/create-notification
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.',
            'errors'  => $validator->errors()
        ], 422)); // 422 Unprocessable Entity
    }
}
<<<<<<< HEAD

=======
>>>>>>> hanh/f6/create-notification
