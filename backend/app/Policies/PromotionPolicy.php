<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Promotion;
use Illuminate\Auth\Access\Response;

class PromotionPolicy
{
    /**
     * Kiểm tra xem user có được tạo chương trình khuyến mãi không.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Kiểm tra xem user có được sửa chương trình khuyến mãi không.
     */
    public function update(User $user, Promotion $promotion): bool
    {
        return $user->hasRole('admin');
    }
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) { 
            return true;
        }
        return null;
    }

    /**
     * Kiểm tra quyền xem danh sách (View Any)
     */
    public function viewAny(User $user): bool
    {
        // Quyền đã được xử lý bởi hàm before
        return false; 
    }

    // ... (Thêm viewAny, delete, v.v. nếu cần)
}