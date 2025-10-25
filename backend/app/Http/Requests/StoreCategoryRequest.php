<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreCategoryRequest extends FormRequest
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
            'name' => 'required|string|max:100|unique:categories,name',
            'parent_id' => 'nullable|integer|exists:categories,id', // Kiểm tra ID danh mục cha có tồn tại không
            'description' => 'nullable|string|max:255',
            'icon' => ['nullable', 'string', 'max:50', 'regex:/^fa[srbld]? fa-[a-z0-9-]+$/'], // Regex kiểm tra format FontAwesome
            'color' => 'nullable|hex_color', // Rule có sẵn của Laravel để check mã màu HEX
            'display_order' => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục không vượt quá 100 ký tự.',
            'description.max' => 'Mô tả không vượt quá 255 ký tự.',
            'icon.regex' => 'Icon không hợp lệ, vui lòng nhập lại.',
            'color.hex_color' => 'Vui lòng chọn màu hợp lệ.'
        ];
    }
    protected function prepareForValidation()
    {
        if ($this->has('title')) {
            $this->merge([
                'title' => trim($this->title)
            ]);
        }
        $this->merge([
            // Nếu không gửi 'is_active', mặc định là true (1)
            'is_visible' => $this->boolean('is_active', true),
            // Nếu không gửi 'display_order', mặc định là 0
            'display_order' => $this->input('display_order', 0),
        ]);
        
        if ($this->has('description')) {
            $this->merge([
                'description' => trim(strip_tags($this->description))
            ]);
        }
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422));
    }
}
