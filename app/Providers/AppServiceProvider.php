<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth; // <-- Import Auth facade
use App\Models\User;    
use Illuminate\Support\Facades\App;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (App::runningInConsole()) {
            return;
        }
        if (app()->isLocal()) {
            // Tìm một user để đăng nhập, ví dụ user có id = 1
            $user = User::find(2); 
            // Hoặc $user = User::where('email', 'admin@example.com')->first();

            // Nếu user tồn tại, thực hiện đăng nhập
            if ($user) {
                Auth::login($user);
            }
        }
    }
}
