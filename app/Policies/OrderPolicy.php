<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models for Admin.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view any models for Seller.
     */
    public function viewAnySeller(User $user): bool
    {
        // Bất kỳ người dùng nào đã đăng nhập đều có thể xem trang đơn hàng của mình
        // (Logic sẽ lọc ra đơn hàng của chính họ trong Controller)
        return true;
    }
}