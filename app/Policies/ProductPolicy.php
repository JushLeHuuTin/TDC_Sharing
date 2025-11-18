<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class ProductPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id;
    }
    public function before(User $user, string $ability): bool|null
    {
        // Nếu user có vai trò là "admin", cho phép tất cả các quyền
        if ($user->role === 'admin') {
            return true;
        }

        return null; // Nếu không phải admin, trả về null để tiếp tục kiểm tra các quyền khác
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
    public function buySelf(User $user, Product $product): bool
    {
        // Trả về FALSE nếu user_id của sản phẩm bằng user_id của người dùng hiện tại
        // Gate::denies('buySelf', $product) sẽ trả về TRUE nếu user_id khớp.
        return $product->user_id !== $user->id;
    }
}
