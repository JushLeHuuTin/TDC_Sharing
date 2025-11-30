<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class UpdateVoucherRequest extends FormRequest
{
    /**
     * Kiểm tra quyền truy cập
     */
    public function authorize(): bool
    {
        Log::info('UpdateVoucherRequest: Running authorize()');
        return true;
    }

    /**
     * Chuẩn bị dữ liệu trước khi validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->input('is_active', false),
            'discount_value' => $this->input('discount_value', 0),
            'discount_max' => $this->input('discount_max', null),
            'min_purchase' => $this->input('min_purchase', 0),
            'usage_limit' => $this->input('usage_limit', 0),
        ]);

        // Convert string -> float nếu cần
        $this->merge([
            'discount_value' => is_string($this->discount_value) ? floatval($this->discount_value) : $this->discount_value,
            'discount_max' => is_string($this->discount_max) ? floatval($this->discount_max) : $this->discount_max,
            'min_purchase' => is_string($this->min_purchase) ? floatval($this->min_purchase) : $this->min_purchase,
        ]);
    }

    /**
     * Các rules validation
     */
    public function rules(): array
    {
        Log::info('UpdateVoucherRequest: Running rules()');

        $voucherId = $this->route('voucher') ?? $this->route('id');
        if ($voucher = $this->route('voucher')) {
            $voucherId = $voucher->id;
        }
        $voucherId = $voucherId ?? 0;

        Log::info('UpdateVoucherRequest: Resolved voucher ID', ['id' => $voucherId]);

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('vouchers', 'code')->ignore($voucherId)],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0.01'],
            'discount_max' => ['nullable', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['required', 'date_format:Y-m-d', 'before:end_date'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after:start_date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validation phức tạp sau khi rules chạy
     */
    public function withValidator($validator): void
    {
        Log::info('UpdateVoucherRequest: Running withValidator()');

        $validator->after(function ($validator) {
            $discountType  = $this->input('discount_type');
            $discountValue = $this->input('discount_value');
            $discountMax   = $this->input('discount_max');

            // percentage <= 100
            if ($discountType === 'percentage' && $discountValue > 100) {
                $validator->errors()->add('discount_value', 'Giá trị giảm (%) không được vượt quá 100.');
            }

            // fixed: discount_max >= discount_value
            if ($discountType === 'fixed' && $discountMax !== null && $discountValue > $discountMax) {
                $validator->errors()->add('discount_max', 'Giá trị tối đa phải lớn hơn hoặc bằng Giá trị giảm.');
            }

            Log::info('UpdateVoucherRequest: Validator data', [
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_max' => $discountMax,
            ]);
        });
    }

    /**
     * Tùy chỉnh thông báo lỗi
     */
    public function messages(): array
    {
        Log::info('UpdateVoucherRequest: Running messages()');

        return [
            'code.required' => 'Mã voucher là bắt buộc.',
            'code.unique' => 'Mã voucher đã tồn tại.',
            'code.max' => 'Mã voucher không quá 50 ký tự.',
            'name.required' => 'Tên voucher là bắt buộc.',
            'name.max' => 'Tên voucher không quá 100 ký tự.',
            'discount_type.required' => 'Loại giảm giá là bắt buộc.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ (percentage hoặc fixed).',
            'discount_value.required' => 'Giá trị giảm là bắt buộc.',
            'discount_value.numeric' => 'Giá trị giảm phải là số.',
            'discount_value.min' => 'Giá trị giảm không hợp lệ.',
            'discount_max.numeric' => 'Giá trị tối đa phải là số.',
            'discount_max.min' => 'Giá trị tối đa không hợp lệ.',
            'min_purchase.numeric' => 'Đơn hàng tối thiểu phải là số.',
            'min_purchase.min' => 'Đơn hàng tối thiểu không hợp lệ.',
            'usage_limit.integer' => 'Số lần dùng phải là số nguyên.',
            'usage_limit.min' => 'Số lần dùng phải ≥ 0.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'start_date.date_format' => 'Ngày bắt đầu không đúng định dạng Y-m-d.',
            'end_date.date_format' => 'Ngày kết thúc không đúng định dạng Y-m-d.',
            'start_date.before' => 'Ngày bắt đầu phải trước ngày kết thúc.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'is_active.boolean' => 'Trạng thái kích hoạt phải đúng hoặc sai (0 hoặc 1).',
        ];
    }

    /**
     * Xử lý lỗi validation
     */
    protected function failedValidation(Validator $validator)
    {
        Log::error('UpdateVoucherRequest: Validation FAILED', [
            'user_id' => Auth::check() ? Auth::id() : 'Guest',
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all(),
        ]);

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Dữ liệu không hợp lệ',
            'errors' => $validator->errors(),
         ], 422));
    }
}
