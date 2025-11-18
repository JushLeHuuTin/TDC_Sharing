<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:100|unique:categories,name',
            'parent_id'     => 'nullable|integer|exists:categories,id',
            'description'   => 'nullable|string|max:255',
            'icon'          => [
                'nullable',
                'string',
                'max:50',
                'regex:/^fa[srlb]? fa-[a-z0-9-]+$/'
            ],
            'color'         => 'nullable|hex_color',
            'display_order' => 'nullable|integer',
            'is_visible'    => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique'   => 'Tên danh mục đã tồn tại.',
            'name.max'      => 'Tên danh mục không vượt quá 100 ký tự.',
            'description.max' => 'Mô tả không vượt quá 255 ký tự.',
            'icon.regex'    => 'Icon không hợp lệ.',
            'color.hex_color' => 'Mã màu không hợp lệ.',
        ];
    }

    protected function prepareForValidation()
    {
        // Chuẩn hoá is_visible
        if ($this->has('is_visible')) {
            $this->merge([
                'is_visible' => filter_var($this->is_visible, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
            ]);
        }

        // Nếu mô tả tồn tại → strip + trim
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

