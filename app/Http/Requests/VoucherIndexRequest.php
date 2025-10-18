<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class VoucherIndexRequest extends FormRequest
{
    /**
     * Ràng buộc 12: Bảo mật - Chỉ Admin mới được xem danh sách này.
     */
    public function authorize(): bool
    {
        // Yêu cầu xác thực và có vai trò 'admin'
        return Auth::check() && Auth::user()->hasRole('admin'); 
    }

    public function rules(): array
    {
        return [
            // Ràng buộc 1: Thanh tìm kiếm (Giới hạn 100 ký tự)
            'search' => ['nullable', 'string', 'max:100'], 

            // Ràng buộc 2: Bộ lọc trạng thái
            'status' => ['nullable', 'string', Rule::in(['active', 'expired', 'inactive'])], 

            // Ràng buộc 3: Bộ lọc loại giảm giá
            'type' => ['nullable', 'string', Rule::in(['percentage', 'fixed'])], 

            // Ràng buộc 9 & 13: Phân trang & Hiệu năng
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],

            // Ràng buộc 10: Sắp xếp
            'sort_by' => ['nullable', 'string', Rule::in(['id', 'code', 'end_date', 'quantity', 'created_at'])],
            'sort_dir' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
    
    /**
     * Ràng buộc 1: Chuẩn hóa keyword (trim, lowercase)
     */
    protected function prepareForValidation(): void
    {
        if ($this->search) {
            $this->merge([
                'search' => strtolower(trim($this->search)),
            ]);
        }
    }
}