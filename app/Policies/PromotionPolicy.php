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

    // ... (Thêm viewAny, delete, v.v. nếu cần)
}