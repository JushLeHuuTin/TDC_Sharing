<?php

namespace App\Policies;

<<<<<<< HEAD:backend/app/Policies/CategoryPolicy.php
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;
=======
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
>>>>>>> hanh/f16/show-total-products:app/Policies/CategoryPolicy.php

class CategoryPolicy
{
    /**
<<<<<<< HEAD:backend/app/Policies/CategoryPolicy.php
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
         return $user->role === 'admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $category): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
         return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {

         return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
         return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $category): bool
    {
         return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Category $category): bool
    {
         return $user->role === 'admin';
    }
=======
     * Determine whether the user can create models.
     * Hàm này sẽ kiểm tra xem user có quyền tạo danh mục mới hay không.
     */
    public function create(User $user): bool
    {
        // Giả sử trong bảng 'users' của bạn có một cột 'role'.
        // Chỉ những user có role là 'admin' mới có quyền tạo.
        // Bạn có thể thay đổi logic này cho phù hợp với hệ thống của bạn.
        return $user->role === 'admin';
    }

    // Các hàm khác cho view, update, delete...
    // public function view(User $user, Category $category): bool
    // { ... }
    // public function update(User $user, Category $category): bool
    // { ... }
    // public function delete(User $user, Category $category): bool
    // { ... }
>>>>>>> hanh/f16/show-total-products:app/Policies/CategoryPolicy.php
}
