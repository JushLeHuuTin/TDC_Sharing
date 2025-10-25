<?php

namespace App\Http\Requests;

<<<<<<< HEAD:backend/app/Http/Requests/StoreCategoryRequest.php
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

=======
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
>>>>>>> hanh/f16/show-total-products:app/Http/Requests/StoreCategoryRequest.php

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
<<<<<<< HEAD:backend/app/Http/Requests/StoreCategoryRequest.php
=======
        // return auth()->check(); // Yêu cầu đăng nhập
>>>>>>> hanh/f16/show-total-products:app/Http/Requests/StoreCategoryRequest.php
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
<<<<<<< HEAD:backend/app/Http/Requests/StoreCategoryRequest.php
            'name' => 'required|string|max:100|unique:categories,name',
            'parent_id' => 'nullable|integer|exists:categories,id', // Kiểm tra ID danh mục cha có tồn tại không
            'description' => 'nullable|string|max:255',
            'icon' => ['nullable', 'string', 'max:50', 'regex:/^fa[srbld]? fa-[a-z0-9-]+$/'], // Regex kiểm tra format FontAwesome
            'color' => 'nullable|hex_color', // Rule có sẵn của Laravel để check mã màu HEX
            'display_order' => 'nullable|integer',
=======
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
>>>>>>> hanh/f16/show-total-products:app/Http/Requests/StoreCategoryRequest.php
            'is_visible' => 'nullable|boolean',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
<<<<<<< HEAD:backend/app/Http/Requests/StoreCategoryRequest.php
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
=======
            'name.unique'   => 'Tên danh mục đã tồn tại.',
            'name.max'      => 'Tên danh mục không vượt quá 150 ký tự.',

            'parent_id.exists' => 'Danh mục cha được chọn không hợp lệ.',

            'description.max' => 'Mô tả không vượt quá 255 ký tự.',

            'color.regex' => 'Vui lòng chọn màu hợp lệ (mã HEX).',

            'display_order.integer' => 'Thứ tự hiển thị phải là một số.',

            'is_visible.boolean' => 'Trạng thái kích hoạt không hợp lệ.',
        ];
    }
>>>>>>> hanh/f16/show-total-products:app/Http/Requests/StoreCategoryRequest.php
}
