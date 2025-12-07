<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize() : bool
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
            // Tiêu đề sản phẩm
            'title' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'regex:/^[a-zA-Z0-9\sàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđĐÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸ\-\(\)\/]+$/',
                Rule::unique('products', 'title')->whereNull('deleted_at'),
            ],
            
            // Giá sản phẩm
            'price' => [
                'required',
                'numeric',
                'min:1000',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            
            // Mô tả
            'description' => [
                'required',
                'string',
                'min:10',
                'max:2000',
                'regex:/^[^<>]*$/', // Không cho phép HTML tags
            ],
            
            // Danh mục
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id'
            ],
            
            // Số lượng tồn kho
            'stocks' => [
                'required',
                'integer',
                'min:0',
                'max:999999'
            ],
            
            // Trạng thái
            'status' => [
                'nullable',
                'in:active,inactive,out_of_stock'
            ],
            
            // Sản phẩm nổi bật
            'is_featured' => [
                'nullable',
                'boolean'
            ],
            
            // Hình ảnh sản phẩm
            'images' => [
                // 'required',
                'array',
                'min:1',
                'max:10'
            ],
            'images.*' => [
                // 'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:2048', // 2MB
            ],
            
            // Ảnh đại diện (index của mảng images)
            'featured_image_index' => [
                'required',
                'integer',
                'min:0'
            ],
            
            // Thuộc tính động (attributes)
            'attributes' => [
                'nullable',
                'array'
            ],
            'attributes.*.attribute_id' => [
                'required_with:attributes',
                'integer',
                'exists:attributes,id'
            ],
            'attributes.*.value' => [
                'required_with:attributes',
                'string',
                'max:255'
            ],
        ];
    }
    public function messages()
    {
        return [
            // Title messages
            'title.required' => 'Tiêu đề sản phẩm không được để trống',
            'title.min' => 'Tiêu đề sản phẩm phải có ít nhất 3 ký tự',
            'title.max' => 'Tiêu đề sản phẩm không được vượt quá 150 ký tự',
            'title.regex' => 'Tiêu đề sản phẩm chứa ký tự không hợp lệ',
            'title.unique' => 'Tiêu đề sản phẩm đã tồn tại trong hệ thống',
            
            // Price messages
            'price.required' => 'Giá sản phẩm không được để trống',
            'price.numeric' => 'Giá sản phẩm phải là số',
            'price.min' => 'Giá sản phẩm phải lớn hơn 1.000 VNĐ',
            'price.max' => 'Giá sản phẩm vượt quá giới hạn cho phép',
            'price.regex' => 'Giá sản phẩm có định dạng không hợp lệ',
            
            // Description messages
            'description.required' => 'Mô tả sản phẩm không được để trống',
            'description.min' => 'Mô tả sản phẩm phải có ít nhất 10 ký tự',
            'description.max' => 'Mô tả sản phẩm không được vượt quá 2000 ký tự',
            'description.regex' => 'Mô tả sản phẩm không được chứa thẻ HTML',
            
            // Category messages
            'category_id.required' => 'Vui lòng chọn danh mục sản phẩm',
            'category_id.integer' => 'Danh mục không hợp lệ',
            'category_id.exists' => 'Danh mục không tồn tại trong hệ thống',
            
            // Stocks messages
            'stocks.required' => 'Số lượng tồn kho không được để trống',
            'stocks.integer' => 'Số lượng tồn kho phải là số nguyên',
            'stocks.min' => 'Số lượng tồn kho không được âm',
            'stocks.max' => 'Số lượng tồn kho vượt quá giới hạn',
            
            // Status messages
            'status.in' => 'Trạng thái sản phẩm không hợp lệ',
            
            // Images messages
            // 'images.required' => 'Vui lòng upload ít nhất 1 hình ảnh sản phẩm',
            // 'images.array' => 'Dữ liệu hình ảnh không hợp lệ',
            // 'images.min' => 'Vui lòng upload ít nhất 1 hình ảnh',
            // 'images.max' => 'Chỉ được upload tối đa 10 hình ảnh',
            // 'images.*.required' => 'File hình ảnh không được để trống',
            // 'images.*.image' => 'File upload phải là hình ảnh',
            // 'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, jpg, png, webp',
            // 'images.*.max' => 'Kích thước hình ảnh không được vượt quá 2MB',
            
            // Featured image messages
            'featured_image_index.required' => 'Vui lòng chọn ảnh đại diện cho sản phẩm',
            'featured_image_index.integer' => 'Chỉ số ảnh đại diện không hợp lệ',
            'featured_image_index.min' => 'Chỉ số ảnh đại diện không hợp lệ',
            
            // Attributes messages
            'attributes.array' => 'Dữ liệu thuộc tính không hợp lệ',
            'attributes.*.attribute_id.required_with' => 'ID thuộc tính không được để trống',
            'attributes.*.attribute_id.integer' => 'ID thuộc tính phải là số nguyên',
            'attributes.*.attribute_id.exists' => 'Thuộc tính không tồn tại',
            'attributes.*.value.required_with' => 'Giá trị thuộc tính không được để trống',
            'attributes.*.value.string' => 'Giá trị thuộc tính phải là chuỗi',
            'attributes.*.value.max' => 'Giá trị thuộc tính không được vượt quá 255 ký tự',
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
        $attributes = $this->input('attributes');

        if ($attributes && is_array($attributes)) {
            $cleanedAttributes = collect($attributes)->map(function ($attribute) {
                
                // Chỉ làm sạch nếu giá trị tồn tại và là chuỗi
                if (isset($attribute['value']) && is_string($attribute['value'])) {
                    $attribute['value'] = trim(strip_tags($attribute['value']));
                }
                return $attribute;
            })->all();
            
            $this->merge([
                'attributes' => $cleanedAttributes
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kiểm tra title không chỉ toàn khoảng trắng
            if ($this->filled('title') && empty(trim($this->title))) {
                $validator->errors()->add('title', 'Tiêu đề sản phẩm không được chỉ chứa khoảng trắng');
            }

            // Kiểm tra description không chỉ toàn khoảng trắng
            if ($this->filled('description') && empty(trim($this->description))) {
                $validator->errors()->add('description', 'Mô tả sản phẩm không được chỉ chứa khoảng trắng');
            }

            // Kiểm tra XSS trong description
            if ($this->filled('description')) {
                $dangerous = ['<script', 'javascript:', 'onerror=', 'onclick=', '<iframe', 'eval('];
                foreach ($dangerous as $pattern) {
                    if (stripos($this->description, $pattern) !== false) {
                        $validator->errors()->add('description', 'Mô tả sản phẩm chứa mã độc hại');
                        break;
                    }
                }
            }

            // Kiểm tra featured_image_index không vượt quá số lượng ảnh
            if ($this->has('images') && $this->has('featured_image_index')) {
                if ($this->featured_image_index >= count($this->images)) {
                    $validator->errors()->add('featured_image_index', 'Ảnh đại diện không hợp lệ');
                }
            }

            // Kiểm tra tên file ảnh nguy hiểm
            if ($this->hasFile('images')) {
                foreach ($this->file('images') as $index => $image) {
                    $originalName = $image->getClientOriginalName();
                    $dangerous_extensions = ['.php', '.exe', '.sh', '.bat', '.cmd', '.js'];
                    
                    foreach ($dangerous_extensions as $ext) {
                        if (stripos($originalName, $ext) !== false) {
                            $validator->errors()->add("images.{$index}", 'Tên file ảnh chứa phần mở rộng nguy hiểm');
                            break;
                        }
                    }

                    // Kiểm tra double extension
                    if (substr_count($originalName, '.') > 1) {
                        $validator->errors()->add("images.{$index}", 'Tên file ảnh không hợp lệ');
                    }
                }
            }

            // Kiểm tra category có tồn tại và visible
            if ($this->filled('category_id')) {
                $category = \App\Models\Category::find($this->category_id);
                if ($category && !$category->is_visible) {
                    $validator->errors()->add('category_id', 'Danh mục này hiện không khả dụng');
                }
            }

            // Kiểm tra attributes thuộc đúng category
            if ($this->filled('attributes') && $this->filled('category_id')) {
                foreach ($this->attributes as $index => $attr) {
                    if (isset($attr['attribute_id'])) {
                        $attribute = \App\Models\Attribute::find($attr['attribute_id']);
                        if ($attribute && $attribute->category_id != $this->category_id) {
                            $validator->errors()->add(
                                "attributes.{$index}.attribute_id", 
                                'Thuộc tính không thuộc danh mục đã chọn'
                            );
                        }
                    }
                }
            }
        });
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
