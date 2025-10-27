<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Auth\Access\Response;

class VoucherPolicy
{
    /**
     * Xác định xem người dùng có thể tạo mô hình hay không.
     * Đây là hàm sẽ được kiểm tra khi bạn gọi $user->can('create', Voucher::class)
     */
    public function create(User $user): bool
    {
        // Giả định rằng Model User của bạn có một phương thức hoặc thuộc tính để kiểm tra vai trò.
        // Ví dụ: kiểm tra vai trò admin
        return $user->role === 'admin';
        
        // Hoặc kiểm tra quyền cụ thể nếu bạn dùng package phân quyền (như Spatie)
        // return $user->hasPermissionTo('create vouchers');
    }

    // Các hàm khác như update, delete, view sẽ được định nghĩa ở đây
}