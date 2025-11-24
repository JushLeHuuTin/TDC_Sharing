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
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Order $order): bool
    {
        $isOrderProcessing = $order->status === 'pending';
        $isSellerOfOrder = $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
        return $isOrderProcessing && $isSellerOfOrder;
    }
/**
     * Determine whether the user can reject the order.
     */
    public function reject(User $user, Order $order): bool
    {
        // Điều kiện: Đơn hàng phải đang 'processing' hoặc 'pending'
         $isOrderCancellable = in_array($order->status, ['processing', 'pending']);

        // Điều kiện: Phải là người bán của đơn hàng đó
        $isSellerOfOrder = $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();

        return $isOrderCancellable && $isSellerOfOrder;
    }
}

