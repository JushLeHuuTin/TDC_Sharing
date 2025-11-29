<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Ai cũng có thể xem danh sách đánh giá
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        return true; // Ai cũng có thể xem chi tiết đánh giá
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Ai đăng nhập rồi đều có thể đánh giá (Controller sẽ check mua hàng sau)
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Cho phép nếu là Admin HOẶC là chính chủ
        return $user->role === 'admin' || $user->id === $review->reviewer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Cho phép nếu là Admin HOẶC là chính chủ
        return $user->role === 'admin' || $user->id === $review->reviewer_id;
    }
}
