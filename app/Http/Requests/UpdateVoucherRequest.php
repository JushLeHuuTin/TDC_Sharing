<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class UpdateVoucherRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có được phép thực hiện request này không.
     * Đảm bảo chỉ Admin mới có quyền sửa voucher.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Kiểm tra quyền Admin/Quản lý voucher
        // Bạn cần triển khai Policy/Middleware để check role 'admin'
        // Giả định middleware 'auth:sanctum' đã được thiết lập.
        return Auth::check() && Auth::user()->hasRole('admin'); 
    }

    /**
     * Định nghĩa các quy tắc validation cho request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        // Lấy ID của voucher đang được sửa từ route
        $voucherId = $this->route('voucher'); 

        return [
            // Ràng buộc 1: Mã voucher (code) - Không sửa, nhưng phải tồn tại và duy nhất (bỏ qua chính nó)
            'code' => [
                'required', 
                'string', 
                'max:20',
                Rule::unique('vouchers', 'code')->ignore($voucherId), // Bỏ qua mã của voucher hiện tại
            ],

            // Ràng buộc 2: Tên voucher (name)
            'name' => ['required', 'string', 'max:150', 'not_regex:/[<>\/]/i'], // Chặn ký tự đặc biệt cơ bản

            // Ràng buộc 3: Mô tả voucher (description)
            'description' => ['nullable', 'string', 'max:255'],

            // Ràng buộc 4: Loại voucher (discount_type)
            'discount_type' => ['required', 'in:percentage,fixed'],

            // Ràng buộc 5: Giá trị giảm (discount_value)
            'discount_value' => ['required', 'numeric', 'min:0.01'],

            // Ràng buộc 6: Giá trị tối đa (discount_max) - DECIMAL
            'discount_max' => ['nullable', 'numeric', 'min:0'],

            // Ràng buộc 7: Đơn hàng tối thiểu (min_purchase) - DECIMAL
            'min_purchase' => ['required', 'numeric', 'min:0'],

            // Ràng buộc 8: Số lượng voucher (quantity)
            'quantity' => ['required', 'integer', 'min:0'], 

            // Ràng buộc 9 & 10: Ngày bắt đầu và Ngày kết thúc
            'start_date' => [
                'required', 
                'date_format:Y-m-d H:i:s', 
                'before:end_date',
                // 'after_or_equal:now', // Có thể bỏ qua nếu cho phép sửa voucher đã bắt đầu
            ],
            'end_date' => [
                'required', 
                'date_format:Y-m-d H:i:s', 
                'after:start_date',
                'after_or_equal:now', // Ngày kết thúc phải sau hoặc bằng hiện tại (tránh voucher hết hạn ngay)
            ],

            // Ràng buộc 11: Khách hàng dùng tối đa (usage_limit)
            'usage_limit' => ['required', 'integer', 'min:0'],

            // Ràng buộc 12: Đối tượng sử dụng (target_audience)
            'target_audience' => ['required', 'string', Rule::in(['all', 'specific_user', 'specific_group'])], // Giả định các loại đối tượng

            // Ràng buộc 13: Kích hoạt (is_active)
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Định nghĩa logic validation phức tạp hơn (ví dụ: so sánh các trường)
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            // Ràng buộc 5 & 6: Nếu là % (percentage), giá trị giảm phải <= 100
            if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 100) {
                $validator->errors()->add('discount_value', 'Giá trị giảm (phần trăm) không được vượt quá 100.');
            }
            
            // Ràng buộc 6: Giá trị tối đa phải >= Giá trị giảm (nếu là fixed)
            if ($data['discount_type'] === 'fixed' && isset($data['discount_max']) && $data['discount_max'] < $data['discount_value']) {
                 $validator->errors()->add('discount_max', 'Giá trị tối đa phải lớn hơn hoặc bằng Giá trị giảm.');
            }
        });
    }

    /**
     * Định nghĩa các thông báo lỗi tùy chỉnh.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // Ràng buộc 1
            'code.unique' => 'Mã voucher đã tồn tại.',
            
            // Ràng buộc 2
            'name.required' => 'Vui lòng nhập tên voucher.',
            'name.max' => 'Tên voucher không được quá 150 ký tự.',
            'name.not_regex' => 'Tên voucher không được chứa ký tự đặc biệt.',

            // Ràng buộc 3
            'description.max' => 'Mô tả không được quá 255 ký tự.',

            // Ràng buộc 4
            'discount_type.in' => 'Vui lòng chọn loại giảm giá hợp lệ (percent hoặc fixed).',
            
            // Ràng buộc 5 & 6 & 7 & 8 & 11
            '*.numeric' => ':attribute phải là giá trị số.',
            '*.integer' => ':attribute phải là số nguyên.',
            'discount_value.min' => 'Giá trị giảm không hợp lệ.',
            'discount_max.min' => 'Giá trị tối đa không hợp lệ.',
            'min_purchase.min' => 'Đơn hàng tối thiểu không hợp lệ.',
            'quantity.min' => 'Số lượng voucher phải lớn hơn hoặc bằng 0.',
            'usage_limit.min' => 'Số lần dùng tối đa phải lớn hơn hoặc bằng 0.',

            // Ràng buộc 9 & 10
            'start_date.before' => 'Ngày bắt đầu phải trước Ngày kết thúc.',
            'end_date.after' => 'Ngày kết thúc phải sau Ngày bắt đầu.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau Ngày hiện tại.',
        ];
    }
}