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
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Kiểm tra quyền sửa/cập nhật voucher.
     */
    public function update(User $user, Voucher $voucher): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Kiểm tra quyền xóa voucher.
     */
    public function delete(User $user, Voucher $voucher): bool
    {
        return $user->hasRole('admin');
    }

    // Các hàm khác như update, delete, view sẽ được định nghĩa ở đây
}