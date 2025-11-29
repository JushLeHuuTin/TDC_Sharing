<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreVoucherRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được ủy quyền thực hiện request này không.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Lấy các quy tắc validation áp dụng cho request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // 1. Mã voucher: Check trùng và không để trống
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers', 'code'),
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            // 3. Mô tả voucher: Nullable
            'description' => 'nullable|string',

            // 4. Loại voucher: Dropdown (enum)
            'discount_type' => [
                'required',
                Rule::in(['percentage', 'fixed']),
            ],

            // 5. Giá trị giảm: Validate > 0
            // Nếu là 'percent', phải <= 100
            'discount_value' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    // Cần lấy giá trị từ trường discount_type đã sửa ở trên
                    if ($this->input('discount_type') === 'percentage' && $value > 100) {
                        $fail('Giá trị giảm (%) không được lớn hơn 100.');
                    }
                },
            ],

            // 7. Đơn hàng tối thiểu: Validate >= 0 (mặc định trong DB là 0)
            'min_purchase' => 'nullable|numeric|min:0',

            // 8. Số lượng voucher: Validate > 0
            'usage_limit' => 'required|integer|min:1|max:100000',

            // 9 & 10. Ngày bắt đầu và Ngày hết hạn
            'start_date' => 'required|date_format:Y-m-d|before:end_date',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',

            // 11. Mỗi KH dùng tối đa: Validate ≥ 1
            // 'max_uses_per_user' => 'required|integer|min:1',

            // 12. Đối tượng sử dụng: Dropdown select (chọn 1 trong các enum)
            // 'target_audience' => [
            //     'required',
            //     Rule::in(['all', 'specific_users', 'students', 'alumni']), // Các giá trị ví dụ
            // ],

            // 13. Kích hoạt ngay: Boolean
            'is_active' => 'boolean',
        ];
    }

    /**
     * Tùy chỉnh thông báo lỗi.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // 1. Mã voucher
            'code.required' => 'Vui lòng nhập mã voucher.',
            'code.unique' => 'Mã voucher đã tồn tại.',

            // 2. Tên voucher
            'name.required' => 'Vui lòng nhập tên voucher.',
            'name.max' => 'Tên voucher không quá 100 ký tự.',

            // 4. Loại voucher
            'discount_type.required' => 'Vui lòng chọn loại voucher.',
            'discount_type.in' => 'Loại voucher không hợp lệ.',

            // 6. Giá trị tối đa
            'discount_value.required' => 'Vui lòng nhập giá trị giảm tối đa.',
            'discount_value.numeric' => 'Giá trị tối đa phải là số.',
            'discount_value.min' => 'Giá trị tối đa không hợp lệ.',

            // 7. Đơn hàng tối thiểu
            'min-purchase.required' => 'Vui lòng nhập đơn hàng tối thiểu.',
            'min-purchase.numeric' => 'Đơn hàng tối thiểu phải là số.',
            'min-purchase.min' => 'Đơn hàng tối thiểu không hợp lệ.',

            // 8. Số lượng voucher
            'quantity.required' => 'Vui lòng nhập số lượng voucher.',
            'quantity.integer' => 'Số lượng voucher phải là số nguyên.',
            'quantity.min' => 'Số lượng voucher phải lớn hơn 0.',

            // 9. Ngày bắt đầu
            'start_date.required' => 'Vui lòng nhập ngày bắt đầu.',
            'start_date.date_format' => 'Ngày bắt đầu không hợp lệ (Format: YYYY-MM-DD HH:MM:SS).',
            'start_date.before' => 'Ngày bắt đầu phải trước ngày hết hạn.',

            // 10. Ngày hết hạn
            'end_date.required' => 'Vui lòng nhập ngày hết hạn.',
            'end_date.date_format' => 'Ngày hết hạn không hợp lệ (Format: YYYY-MM-DD HH:MM:SS).',
            'end_date.after' => 'Ngày hết hạn phải sau ngày bắt đầu.',

            // 11. Mỗi KH dùng tối đa
            'usage_limit.required' => 'Vui lòng nhập số lần sử dụng tối đa.',
            'usage_limit.integer' => 'Số lần sử dụng phải là số nguyên.',
            'usage_limit.min' => 'Số lần sử dụng phải ≥ 1.',

            // 12. Đối tượng sử dụng
            'target_audience.required' => 'Vui lòng chọn đối tượng sử dụng.',
            'target_audience.in' => 'Đối tượng sử dụng không hợp lệ.',

            // 13. Kích hoạt ngay
            'is_active.boolean' => 'Trạng thái kích hoạt không hợp lệ.',
        ];
    }
    protected function prepareForValidation(): void
    {
        // Làm sạch XSS và chuẩn hóa cho name, code, description
        $cleanedCode = $this->input('code') ? strtoupper(strip_tags(trim($this->input('code')))) : null;
        $cleanedName = strip_tags(trim($this->input('name')));
        $cleanedDescription = strip_tags($this->input('description'));

        $this->merge([
            // 1. Bảo vệ và chuẩn hóa Mã, Tên, Mô tả
            'code' => $cleanedCode,
            'name' => $cleanedName,
            'description' => $cleanedDescription,

            // 2. Ép kiểu và gán giá trị mặc định an toàn
            'is_active' => $this->is_active ?? false,

            // 3. Đảm bảo các giá trị numeric là số thực
            'discount_value' => is_string($this->discount_value) ? floatval($this->discount_value) : $this->discount_value,
            'max_value' => is_string($this->max_value) ? floatval($this->max_value) : $this->max_value,
            'min_purchase' => is_string($this->min_purchase) ? floatval($this->min_purchase) : $this->min_purchase,
        ]);
    }
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Lỗi xác thực dữ liệu đầu vào.',
            'errors' => $validator->errors(),
        ], 422)); // 422 Unprocessable Content
    }
}
