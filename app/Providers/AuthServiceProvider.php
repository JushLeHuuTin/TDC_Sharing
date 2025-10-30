<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Product;
use App\Policies\ProductPolicy;
use App\Models\Order;
use App\Models\Voucher;
use App\Policies\OrderPolicy;
use App\Policies\VoucherPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
        \App\Models\Voucher::class => \App\Policies\VoucherPolicy::class,
        \App\Models\Voucher::class => \App\Policies\VoucherPolicy::class,
        'App\Models\Order' => 'App\Policies\OrderPolicy',
        // ... (VoucherPolicy nếu đã tạo)
        Voucher::class => VoucherPolicy::class,

        // 🔥 ĐĂNG KÝ ORDER POLICY
        Order::class => OrderPolicy::class,
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
        $this->registerPolicies();
    }
}
