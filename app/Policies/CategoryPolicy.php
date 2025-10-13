<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CategoryPolicy
{
    /**
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
}
