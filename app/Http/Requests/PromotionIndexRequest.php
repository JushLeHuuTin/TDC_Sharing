<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class PromotionIndexRequest extends FormRequest
{
    /**
     * Chỉ Admin mới được xem danh sách này.
     */
    public function authorize(): bool
    {
        // Kiểm tra quyền (Đã được xử lý bởi Policy/Middleware)
        return Auth::check() && Auth::user()->hasRole('admin'); 
    }

    public function rules(): array
    {
        return [
            // Ràng buộc 1: Thanh tìm kiếm (theo tên)
            'search' => ['nullable', 'string', 'max:100'], 

            // Ràng buộc 2: Bộ lọc trạng thái
            'status' => ['nullable', 'string', Rule::in(['active', 'expired', 'upcoming'])], 

            // Ràng buộc 3: Bộ lọc loại giảm giá
            'type' => ['nullable', 'string', Rule::in(['percentage', 'fixed', 'freeship'])], 

            // Phân trang
            'per_page' => ['nullable', 'integer', 'min:5', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
    
    /**
     * Ràng buộc 1: Chuẩn hóa keyword
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