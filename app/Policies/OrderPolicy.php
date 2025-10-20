<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Chỉ cho phép user có vai trò 'admin' xem danh sách.
        return $user->role === 'admin';
    }
      /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // Cho phép xem nếu người dùng là Admin
        if ($user->role === 'admin') {
            return true;
        }

        // Hoặc, cho phép xem nếu đơn hàng có chứa ít nhất một sản phẩm của người bán này
        return $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
    }
}