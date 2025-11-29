<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() : bool
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
            'title' => 'required|string|max:150',
            'price' => 'required|numeric|gt:0|max:999999999', // gt:0 nghĩa là > 0
            'description' => 'nullable|string|max:2000',
            'status' => ['required', Rule::in(['active','new','draft','pending','sold','hidden'])], // Chỉ chấp nhận các giá trị này
        ];
    }
    public function messages()
    {
        return [
            'title.required' => 'Vui lòng nhập tên sản phẩm.',
            'title.max' => 'Tên sản phẩm không quá 150 ký tự.',
            'price.required' => 'Vui lòng nhập giá sản phẩm.',
            'price.numeric' => 'Giá sản phẩm phải là một con số.',
            'price.gt' => 'Giá sản phẩm phải lớn hơn 0.',
            'price.max' => 'Giá không được vượt quá 999,999,999đ.',
            'description.max' => 'Mô tả không vượt quá 2000 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Vui lòng chọn trạng thái hợp lệ.',
        ];
    }

    protected function prepareForValidation()
    {
        // Trim whitespace và clean input
        if ($this->has('title')) {
            $this->merge([
                'title' => trim($this->title)
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => trim(strip_tags($this->description))
            ]);
        }
    }

    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         // Kiểm tra title không chỉ toàn khoảng trắng
    //         if ($this->filled('title') && empty(trim($this->title))) {
    //             $validator->errors()->add('title', 'Tiêu đề sản phẩm không được chỉ chứa khoảng trắng');
    //         }

    //         // Kiểm tra description không chỉ toàn khoảng trắng
    //         if ($this->filled('description') && empty(trim($this->description))) {
    //             $validator->errors()->add('description', 'Mô tả sản phẩm không được chỉ chứa khoảng trắng');
    //         }

    //         // Kiểm tra XSS trong description
    //         if ($this->filled('description')) {
    //             $dangerous = ['<script', 'javascript:', 'onerror=', 'onclick=', '<iframe', 'eval('];
    //             foreach ($dangerous as $pattern) {
    //                 if (stripos($this->description, $pattern) !== false) {
    //                     $validator->errors()->add('description', 'Mô tả sản phẩm chứa mã độc hại');
    //                     break;
    //                 }
    //             }
    //         }

    //         // Kiểm tra featured_image_index không vượt quá số lượng ảnh
    //         if ($this->has('images') && $this->has('featured_image_index')) {
    //             if ($this->featured_image_index >= count($this->images)) {
    //                 $validator->errors()->add('featured_image_index', 'Ảnh đại diện không hợp lệ');
    //             }
    //         }

    //         // Kiểm tra tên file ảnh nguy hiểm
    //         if ($this->hasFile('images')) {
    //             foreach ($this->file('images') as $index => $image) {
    //                 $originalName = $image->getClientOriginalName();
    //                 $dangerous_extensions = ['.php', '.exe', '.sh', '.bat', '.cmd', '.js'];
                    
    //                 foreach ($dangerous_extensions as $ext) {
    //                     if (stripos($originalName, $ext) !== false) {
    //                         $validator->errors()->add("images.{$index}", 'Tên file ảnh chứa phần mở rộng nguy hiểm');
    //                         break;
    //                     }
    //                 }

    //                 // Kiểm tra double extension
    //                 if (substr_count($originalName, '.') > 1) {
    //                     $validator->errors()->add("images.{$index}", 'Tên file ảnh không hợp lệ');
    //                 }
    //             }
    //         }

    //         // Kiểm tra category có tồn tại và visible
    //         if ($this->filled('category_id')) {
    //             $category = \App\Models\Category::find($this->category_id);
    //             if ($category && !$category->is_visible) {
    //                 $validator->errors()->add('category_id', 'Danh mục này hiện không khả dụng');
    //             }
    //         }

    //         // Kiểm tra attributes thuộc đúng category
    //         if ($this->filled('attributes') && $this->filled('category_id')) {
    //             foreach ($this->attributes as $index => $attr) {
    //                 if (isset($attr['attribute_id'])) {
    //                     $attribute = \App\Models\Attribute::find($attr['attribute_id']);
    //                     if ($attribute && $attribute->category_id != $this->category_id) {
    //                         $validator->errors()->add(
    //                             "attributes.{$index}.attribute_id", 
    //                             'Thuộc tính không thuộc danh mục đã chọn'
    //                         );
    //                     }
    //                 }
    //             }
    //         }
    //     });
    // }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors()
        ], 422));
    }
}
