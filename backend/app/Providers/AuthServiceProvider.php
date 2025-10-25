<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Product;
<<<<<<< HEAD:backend/app/Providers/AuthServiceProvider.php
use App\Models\Category;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
=======
use App\Models\Review;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ReviewPolicy;
use App\Models\Notification;
use App\Policies\NotificationPolicy;
use App\Models\Order; // Thêm dòng này
use App\Policies\OrderPolicy; // Thêm dòng này
use App\Policies\DashboardPolicy; // <-- Import DashboardPolicy
use Illuminate\Support\Facades\Gate; // <-- Import Gate
>>>>>>> hanh/f16/show-total-products:app/Providers/AuthServiceProvider.php
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
<<<<<<< HEAD:backend/app/Providers/AuthServiceProvider.php
        Category::class => CategoryPolicy::class,
=======
        Category::class => CategoryPolicy::class, // Đăng ký CategoryPolicy
        Review::class => ReviewPolicy::class, // Đăng ký CategoryPolicy
        Notification::class => NotificationPolicy::class,
        Order::class => OrderPolicy::class,
>>>>>>> hanh/f16/show-total-products:app/Providers/AuthServiceProvider.php
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // ĐỊNH NGHĨA MỘT QUYỀN MỚI TÊN LÀ 'viewAdminDashboard'
       Gate::define('viewAdminDashboard', [DashboardPolicy::class, 'viewAdminDashboard']);
    }
}
