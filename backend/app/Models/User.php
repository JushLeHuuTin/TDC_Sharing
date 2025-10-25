<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email', 'password', 'phone', 'full_name', 
        'status', 'role', 'username'
    ];

    protected $hidden = ['password'];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
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

    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
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
