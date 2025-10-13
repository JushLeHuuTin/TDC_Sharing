<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can create reviews.
     * Logic ở đây là để kiểm tra xem người dùng có quyền tạo review cho sản phẩm này không.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Product $product
     * @return bool
     */
    public function create(User $user, Product $product): bool
    {
        // Ví dụ logic nâng cao: Kiểm tra xem người dùng đã từng mua sản phẩm này chưa.
        // Bạn cần tự định nghĩa hàm `hasPurchased` trong model User.
        // return $user->hasPurchased($product);

        // Logic cơ bản: Bất kỳ người dùng nào đã đăng nhập đều có thể viết đánh giá.
        // Đây là phương án đơn giản hơn để bắt đầu.
        return true;
    }
}