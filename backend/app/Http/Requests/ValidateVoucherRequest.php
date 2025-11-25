<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ValidateVoucherRequest extends FormRequest
{

    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'voucher_code' => ['nullable', 'string', 'max:50'], 
        ];
    }
    public function messages(): array
    {
        return [
            'voucher_code.string' => 'Mã giảm giá phải là một chuỗi ký tự.',
            'voucher_code.max' => 'Mã giảm giá không được vượt quá :max ký tự.',
        ];
    }
}