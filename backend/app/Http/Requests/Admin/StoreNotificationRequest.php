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
     * Logic phân quyền sẽ được xử lý bởi Policy, ở đây chỉ cần trả về true.
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
            'type'       => ['required', 'string', Rule::in(['order', 'promotion', 'system', 'message'])],
            'content'    => ['required', 'string', 'max:255'],
        ];
    }
    protected function prepareForValidation()
    {
        // ✨ CẬP NHẬT: strip_tags và trim cho cả name và description
        if ($this->has('content')) {
            $this->merge([
                'content' => trim(strip_tags($this->content))
            ]);
        }
        // if ($this->has('name')) {
        //     $this->merge([
        //         'name' => trim(strip_tags($this->name))
        //     ]);
        // }
    }
     /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Vui lòng chọn loại thông báo.',
            'content.required' => 'Vui lòng nhập nội dung thông báo.',
            'content.max' => 'Nội dung thông báo không vượt quá 255 ký tự.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ.',
            'errors'  => $validator->errors()
        ], 422)); // 422 Unprocessable Entity
    }
}
