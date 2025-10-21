<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{

    /**
     * Determine whether the user can view any models. (FOR ADMIN)
     */
    public function viewAny(User $user): bool
    {
        // Chỉ cho phép user có vai trò 'admin' xem danh sách tất cả đơn hàng.
        return $user->role === 'admin';
    }

    /**
     * SỬA LỖI: Bổ sung lại phương thức còn thiếu này.
     * Determine whether the user can view any models. (FOR SELLER)
     */
    public function viewAnySeller(User $user): bool
    {
        // Logic cho C2C: "Người bán" là người dùng đã đăng ít nhất một sản phẩm.
        return $user->products()->exists();
    }

    /**
     * Determine whether the user can view the model. (FOR DETAILS)
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
    /**
     * PHẦN MỚI: Determine whether the user can approve the model.
     */
    public function approve(User $user, Order $order): bool
    {
        // Điều kiện để duyệt đơn:
        // 1. Trạng thái hiện tại của đơn hàng phải là 'processing'.
        // 2. Người dùng phải là người bán của ít nhất một sản phẩm trong đơn hàng đó.
        $isOrderProcessing = $order->status === 'processing';

        $isSellerOfOrder = $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();

        return $isOrderProcessing && $isSellerOfOrder;
    }
}

