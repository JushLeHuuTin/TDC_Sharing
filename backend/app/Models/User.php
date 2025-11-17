<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // Thêm nếu dùng Notifications
use Laravel\Sanctum\HasApiTokens;       // Thêm để dùng Sanctum
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // Bỏ SoftDeletes nếu không dùng
    // use SoftDeletes; // Bỏ comment nếu dùng

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'password',
        'phone',
        'full_name',
        'status',
        'role',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token', // Thêm nếu có
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // Thêm nếu có
        'password' => 'hashed',            // Quan trọng cho bảo mật
    ];

    // ... (các relationships khác của bạn: addresses, products, cart, orders, ...) ...

    /**
     * Mối quan hệ: Một người dùng viết nhiều đánh giá.
     */
    public function reviews(): HasMany
    {
        // Liên kết đến cột reviewer_id trong bảng reviews
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Mối quan hệ: Một người dùng (người bán) có nhiều sản phẩm.
     * Cần thiết cho logic C2C trong OrderPolicy::viewAnySeller
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function favorites(): BelongsToMany
    {
        // Định nghĩa quan hệ nhiều-nhiều với Product
        // 'wishlist' là tên bảng trung gian trong DB của bạn
        return $this->belongsToMany(Product::class, 'wishlists', 'user_id', 'product_id')
            ->withTimestamps(); // Lấy cả thời gian yêu thích
    }
}
