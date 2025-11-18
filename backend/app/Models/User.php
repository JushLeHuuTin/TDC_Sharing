<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // Thêm nếu dùng Notifications
use Laravel\Sanctum\HasApiTokens;       // Thêm để dùng Sanctum
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // Bỏ SoftDeletes nếu không dùng
    protected $fillable = [
        'email', 'password', 'phone', 'full_name', 
        'status', 'role', 'username'
    ];
    protected $casts = [
        'email_verified_at' => 'datetime', // Thêm nếu có
        'password' => 'hashed',            // Quan trọng cho bảo mật
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

}