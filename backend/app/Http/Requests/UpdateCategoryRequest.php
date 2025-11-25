<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator; 
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Luôn cho phép request đi tiếp, việc phân quyền chi tiết sẽ do Policy xử lý.
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
        // Lấy ID của danh mục đang được cập nhật từ route
        $categoryId = $this->route('category')->id;
        return [
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('categories')->ignore($categoryId), // Tên phải là duy nhất, NGOẠI TRỪ chính nó
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id', // Phải là một ID danh mục hợp lệ
                Rule::notIn([$categoryId]), // Không được chọn chính nó làm cha
            ],
            'description' => 'nullable|string|max:255',
            'icon' => ['nullable', 'string', 'max:100', 'regex:/^fa[srbld]? fa-[a-z0-9-]+$/'],
            'color' => 'nullable|hex_color',
            'display_order' => 'nullable|integer|min:0', // Phải là số không âm
            'is_visible' => 'nullable|boolean',
            
        ];
    }

    /**
     * Tùy chỉnh các thông báo lỗi.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.unique' => 'Tên danh mục này đã tồn tại.',
            'name.max' => 'Tên danh mục không vượt quá 150 ký tự.',
            'parent_id.not_in' => 'Không thể chọn chính danh mục này làm danh mục cha.',
            'description.max' => 'Mô tả không vượt quá 255 ký tự.',
            'icon.regex' => 'Icon không hợp lệ, vui lòng nhập lại theo chuẩn FontAwesome.',
            'color.hex_color' => 'Vui lòng chọn màu hợp lệ.',
            'display_order.min' => 'Thứ tự hiển thị phải là số không âm.',
        ];
    }
    protected function prepareForValidation()
    {
        if ($this->has('is_visible')) {
            $this->merge([
                'is_visible' => filter_var($this->is_visible, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        // ✨ CẬP NHẬT: strip_tags và trim cho cả name và description
        if ($this->has('description')) {
            $this->merge([
                'description' => trim(strip_tags($this->description))
            ]);
        }
        if ($this->has('name')) {
            $this->merge([
                'name' => trim(strip_tags($this->name))
            ]);
        }
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Dữ liệu không hợp lệ.',
            'errors'      => $validator->errors()
        ], 422)); // 422 Unprocessable Entity là mã lỗi chuẩn cho validation
    }
}