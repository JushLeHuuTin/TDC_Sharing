<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Product;
use App\Models\Review;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ReviewPolicy;
use App\Models\Order; // Thêm dòng này
use App\Policies\OrderPolicy; // Thêm dòng này
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class, // Đăng ký CategoryPolicy
        Review::class => ReviewPolicy::class, // Đăng ký CategoryPolicy
        Order::class => OrderPolicy::class, // Thêm dòng này
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
        //
    }
}
