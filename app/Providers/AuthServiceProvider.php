<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Product;
use App\Models\Review;
use App\Policies\ProductPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ReviewPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
        Category::class => CategoryPolicy::class, // Đăng ký CategoryPolicy
        Review::class => ReviewPolicy::class, // Đăng ký CategoryPolicy
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
