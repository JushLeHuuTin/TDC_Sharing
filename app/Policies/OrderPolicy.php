<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Cho phép admin hoặc người dùng có vai trò đặc biệt bỏ qua kiểm tra Policy.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Giả định hàm isAdmin() có tồn tại trên Model User của bạn
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Xác định xem người dùng có thể xem đơn hàng cụ thể không.
     */
    public function view(User $user, Order $order): bool
    {
        // 1. Kiểm tra người mua
        if ($user->id === $order->buyer_id) {
            return true;
        }

        // 2. Kiểm tra người bán liên quan (Nếu người dùng là người bán của bất kỳ sản phẩm nào trong đơn hàng)
        $isRelatedSeller = $order->items->contains('seller_id', $user->id);
        if ($isRelatedSeller) {
            return true;
        }

        return false;
    }

    // ... Các hàm policy khác: update, delete, create
}
