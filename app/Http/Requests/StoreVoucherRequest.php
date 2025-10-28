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
                'max:255',
                Rule::unique('vouchers', 'code'), // Check trùng trong bảng 'vouchers'
            ],            
            // 3. Mô tả voucher: Nullable
            'description' => 'nullable|string',

            // 4. Loại voucher: Dropdown (enum)
            'type' => [
                'required',
                Rule::in(['percent', 'fixed']), // Chỉ chấp nhận 'percent' hoặc 'fixed'
            ],

            // 5. Giá trị giảm: Validate > 0
            // Nếu là 'percent', phải <= 100
            'value' => [
                'required',
                'numeric',
                'min:0.01',
                // Quy tắc tùy chỉnh cho loại phần trăm
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'percent' && $value > 100) {
                        $fail('Giá trị giảm (%) không được lớn hơn 100.');
                    }
                },
            ],

            // 6. Giá trị tối đa: Validate >= 0 (mặc định trong DB là 0)
            'max_discount_value' => 'required|numeric|min:0',

            // 7. Đơn hàng tối thiểu: Validate >= 0 (mặc định trong DB là 0)
            'min_order_value' => 'required|numeric|min:0',

            // 8. Số lượng voucher: Validate > 0
            'quantity' => 'required|integer|min:1',

            // 9 & 10. Ngày bắt đầu và Ngày hết hạn
            'start_date' => 'required|date_format:Y-m-d H:i:s|before:end_date',
            'end_date' => 'required|date_format:Y-m-d H:i:s|after:start_date',

            // 11. Mỗi KH dùng tối đa: Validate ≥ 1
            'max_uses_per_user' => 'required|integer|min:1',

            // 12. Đối tượng sử dụng: Dropdown select (chọn 1 trong các enum)
            'target_audience' => [
                'required',
                Rule::in(['all', 'specific_users', 'students', 'alumni']), // Các giá trị ví dụ
            ],

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
            'type.required' => 'Vui lòng chọn loại voucher.',
            'type.in' => 'Loại voucher không hợp lệ.',

            // 5. Giá trị giảm
            'value.required' => 'Vui lòng nhập giá trị giảm.',
            'value.numeric' => 'Giá trị giảm phải là số.',
            'value.min' => 'Giá trị giảm không hợp lệ.', // Bắt lỗi > 0

            // 6. Giá trị tối đa
            'max_discount_value.required' => 'Vui lòng nhập giá trị giảm tối đa.',
            'max_discount_value.numeric' => 'Giá trị tối đa phải là số.',
            'max_discount_value.min' => 'Giá trị tối đa không hợp lệ.',

            // 7. Đơn hàng tối thiểu
            'min_order_value.required' => 'Vui lòng nhập đơn hàng tối thiểu.',
            'min_order_value.numeric' => 'Đơn hàng tối thiểu phải là số.',
            'min_order_value.min' => 'Đơn hàng tối thiểu không hợp lệ.',

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
            'max_uses_per_user.required' => 'Vui lòng nhập số lần sử dụng tối đa.',
            'max_uses_per_user.integer' => 'Số lần sử dụng phải là số nguyên.',
            'max_uses_per_user.min' => 'Số lần sử dụng phải ≥ 1.',

            // 12. Đối tượng sử dụng
            'target_audience.required' => 'Vui lòng chọn đối tượng sử dụng.',
            'target_audience.in' => 'Đối tượng sử dụng không hợp lệ.',

            // 13. Kích hoạt ngay
            'is_active.boolean' => 'Trạng thái kích hoạt không hợp lệ.',
        ];
    }
    protected function prepareForValidation(): void
    {
        // 13. Kích hoạt ngay: Boolean (mặc định = false nếu không chọn)
        $this->merge([
            'is_active' => $this->is_active ?? false,
            // 11. Đảm bảo giá trị tối thiểu là 1 nếu không truyền vào (mặc dù đã có min:1 trong rules)
            'max_uses_per_user' => $this->max_uses_per_user ?? 1,
            // 6. Đảm bảo giá trị tối đa là 0 nếu không truyền vào
            'max_discount_value' => $this->max_discount_value ?? 0,
            // 7. Đơn hàng tối thiểu
            'min_order_value' => $this->min_order_value ?? 0,
            // Đảm bảo các giá trị numeric là số thực
            'value' => is_string($this->value) ? floatval($this->value) : $this->value,
            'max_discount_value' => is_string($this->max_discount_value) ? floatval($this->max_discount_value) : $this->max_discount_value,
            'min_order_value' => is_string($this->min_order_value) ? floatval($this->min_order_value) : $this->min_order_value,
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