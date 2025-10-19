<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return auth()->check(); // Yêu cầu đăng nhập
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
            // 1. Tên danh mục: Bắt buộc, là chuỗi, tối đa 150 ký tự, và không được trùng trong bảng 'categories'
            'name' => 'required|string|max:100|unique:categories,name',

            // 2. Danh mục cha: Không bắt buộc, phải là số nguyên, và ID phải tồn tại trong bảng 'categories'
            'parent_id' => 'nullable|integer|exists:categories,id',

            // 3. Mô tả: Không bắt buộc, là chuỗi, tối đa 255 ký tự
            'description' => 'nullable|string|max:255',

            // 4. Icon: Không bắt buộc, là chuỗi, tối đa 100 ký tự (đủ cho class FontAwesome)
            'icon' => 'nullable|string|max:100',

            // 5. Màu sắc: Không bắt buộc, là chuỗi, phải đúng định dạng mã màu HEX
            'color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],

            // 6. Thứ tự hiển thị: Không bắt buộc, phải là số nguyên
            'display_order' => 'nullable|integer',

            // 7. Kích hoạt: Không bắt buộc, phải là giá trị boolean (true/false, 1/0)
            'is_visible' => 'nullable|boolean',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique'   => 'Tên danh mục đã tồn tại.',
            'name.max'      => 'Tên danh mục không vượt quá 150 ký tự.',

            'parent_id.exists' => 'Danh mục cha được chọn không hợp lệ.',

            'description.max' => 'Mô tả không vượt quá 255 ký tự.',

            'color.regex' => 'Vui lòng chọn màu hợp lệ (mã HEX).',

            'display_order.integer' => 'Thứ tự hiển thị phải là một số.',

            'is_visible.boolean' => 'Trạng thái kích hoạt không hợp lệ.',
        ];
    }
}
