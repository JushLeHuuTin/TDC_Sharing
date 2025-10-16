<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider; 
use App\Models\Product;
use App\Policies\ProductPolicy;
class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Product::class => ProductPolicy::class,
        \App\Models\Voucher::class => \App\Policies\VoucherPolicy::class,
        'App\Models\Voucher' => 'App\Policies\VoucherPolicy',
    'App\Models\Order' => 'App\Policies\OrderPolicy',
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
