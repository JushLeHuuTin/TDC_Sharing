<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartItemPolicy
{
    /**
     * Cho phép admin hoặc người dùng có vai trò đặc biệt bỏ qua kiểm tra Policy.
     */
   
    /**
     * Xác định xem người dùng có thể xem đơn hàng cụ thể không.
     */
   public function delete(User $user, CartItem $cartItem): bool
    {

        // Trả về TRUE nếu ID của người sở hữu Cart (liên kết với CartItem) trùng với ID của User hiện tại
        return $cartItem->cart->user_id === $user->id;
    }

    // ... Các hàm policy khác: update, delete, create
}
