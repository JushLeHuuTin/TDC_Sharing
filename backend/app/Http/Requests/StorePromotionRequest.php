<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class StorePromotionRequest extends FormRequest
{
    /**
     * Chỉ Admin mới được phép tạo chương trình khuyến mãi.
     */
    public function authorize(): bool
    {
        return Auth::check(); 
    }
    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strip_tags($this->input('name')),
            'description' => strip_tags($this->input('description')),
        ]);
    }
    /**
     * Định nghĩa các quy tắc validation cho request.
     */
    public function rules(): array
    {
        return [
            // Ràng buộc 1: Tên chương trình (Check trùng, max 100)
            'name' => ['required', 'string', 'max:100', 'unique:promotions,name', 'not_regex:/[<>\/]/i'],

            // Ràng buộc 2: Mô tả chương trình (nullable, max 500)
            'description' => ['nullable', 'string', 'max:500'],

            // Ràng buộc 3: Loại giảm (percentage/fixed)
            'discount_type' => ['required', 'string', Rule::in(['percentage', 'fixed'])],

            // Ràng buộc 4: Giá trị giảm (Chỉ số dương)
            'discount_value' => ['required', 'numeric', 'min:0.01'],

            // Ràng buộc 5: Giá trị tối đa
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'numeric', 'min:0'],

            // Ràng buộc 6: Đơn hàng tối thiểu
            'min_purchase' => ['required', 'numeric', 'min:0'],

            // Ràng buộc 7: Ngày bắt đầu
            'start_date' => [
                'required', 
                'date_format:Y-m-d', 
                'before:end_date',
                'after_or_equal:' . Carbon::now()->subMinutes(1)->format('Y-m-d') // Lớn hơn hoặc bằng hiện tại
            ],

            // Ràng buộc 8: Ngày kết thúc
            'end_date' => [
                'required', 
                'date_format:Y-m-d', 
                'after:start_date',
                'after_or_equal:' . Carbon::now()->subMinutes(1)->format('Y-m-d')
            ],

            // Ràng buộc 9: Khách hàng dùng tối đa
            'per_customer_limit' => ['required', 'integer', 'min:1'],

            // Ràng buộc 10: Đối tượng sử dụng
            'target_audiences' => [ 'array'],
            'target_audiences.*' => ['integer', 'exists:users,id'], // Giả định có bảng user_groups

            // Ràng buộc 11: Danh mục áp dụng
            'category_ids' => [ 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'], // Kiểm tra ID danh mục tồn tại
        ];
    }
    
    /**
     * Validation phức tạp hơn (So sánh các trường)
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $discountType = $this->input('discount_type');
            $discountValue = $this->input('discount_value');
            $maxDiscount = $this->input('max_discount');
    
            // Ràng buộc 3: Nếu discount_type được gửi và là 'percentage'
            if ($discountType === 'percentage' && $discountValue > 100) {
                $validator->errors()->add('discount_value', 'Phần trăm giảm không vượt quá 100%.');
            }
            
            // Ràng buộc 5: Nếu là 'fixed' và có max_discount
            // Ta dùng !is_null() để kiểm tra sự tồn tại an toàn
            if ($discountType === 'fixed' && !is_null($maxDiscount) && $maxDiscount < $discountValue) {
                 $validator->errors()->add('max_discount', 'Giá trị tối đa phải lớn hơn hoặc bằng Giá trị giảm.');
            }
        });
    }

    /**
     * Thông báo lỗi tùy chỉnh.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên chương trình.',
            'name.max' => 'Tên chương trình không quá 100 ký tự.',
            'name.unique' => 'Tên chương trình đã tồn tại.',
            'name.not_regex' => 'Tên chương trình chứa ký tự đặc biệt không hợp lệ.',

            'description.max' => 'Mô tả không quá 500 ký tự.',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá.',
            
            'discount_value.required' => 'Vui lòng nhập giá trị giảm hợp lệ.',
            'discount_value.min' => 'Giá trị giảm phải là số dương.',
            
            'max_uses_per_user.min' => 'Số lần sử dụng phải lớn hơn 0.',
            'start_date.after_or_equal' => 'Ngày bắt đầu phải lớn hơn hoặc bằng ngày hiện tại.',
            'end_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',

            'target_audiences.required' => 'Vui lòng chọn đối tượng sử dụng.',
            'category_ids.required' => 'Vui lòng chọn danh mục áp dụng hợp lệ.',
            'category_ids.*.exists' => 'Danh mục đã chọn không hợp lệ.',
        ];
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