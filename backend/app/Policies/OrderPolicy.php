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

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Order $order): bool
    {
        $isOrderProcessing = $order->status === 'processing';
        $isSellerOfOrder = $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
        return $isOrderProcessing && $isSellerOfOrder;
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, Order $order): bool
    {
        // Logic tương tự như duyệt đơn
        $isOrderProcessing = $order->status === 'processing';
        $isSellerOfOrder = $order->items()->whereHas('product', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->exists();
        return $isOrderProcessing && $isSellerOfOrder;
    }
    public function view(User $user, Order $order): Response // Thay đổi kiểu trả về thành Response
    {
        // 1. Kiểm tra người mua
        if ($user->id === $order->buyer_id) {
            return Response::allow();
        }

        // 2. Kiểm tra người bán liên quan (Nếu người dùng là người bán của bất kỳ sản phẩm nào trong đơn hàng)

        // Sử dụng Query Builder thay vì Collection methods để tối ưu hóa truy vấn
        $isRelatedSeller = $order->orderItems()
            ->whereHas('product', function ($query) use ($user) {
                // Giả định OrderItem có mối quan hệ Product, và Product có trường user_id (seller_id)
                $query->where('user_id', $user->id);
            })
            ->exists();

        if ($isRelatedSeller) {
            return Response::allow();
        }

        // Nếu không phải người mua và không phải người bán liên quan
        return Response::deny('Bạn không có quyền truy cập vào đơn hàng này.');
    }
    public function viewAny(User $user): bool
    {
        return $user->role === 'customer' || $user->role === 'seller';
    }
    public function create(User $user): bool
    {
        return $user->role === 'customer';
    }
}
