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

    // ... Các hàm policy khác: update, delete, create
}
