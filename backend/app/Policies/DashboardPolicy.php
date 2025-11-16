<?php
// Vị trí: app/Policies/DashboardPolicy.php

namespace App\Policies;

use App\Models\User;

class DashboardPolicy
{
    /**
     * Determine whether the user can view the admin dashboard.
     */
    public function viewAdminDashboard(User $user): bool
    {
        // Chỉ cho phép user có vai trò 'admin' xem dashboard.
        return $user->role === 'admin';
    }
}