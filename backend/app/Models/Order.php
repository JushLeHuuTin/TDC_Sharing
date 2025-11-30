<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
         'id',    
        'user_id',   
        'seller_id', 
        'payment_method',
        'address_id', 
        'voucher_id',  
        'status',       
        'total_amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    // Quan hệ này có thể dùng cho mục đích khác, nhưng logic review đang dùng 'orderItems'
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // --- QUAN TRỌNG: Thêm hàm này để Controller hiểu đơn hàng có nhiều sản phẩm ---
    // Hàm này giúp câu lệnh whereHas('orderItems') trong ReviewController hoạt động
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTotalPrice()
    {
        return $this->items->sum('subtotal');
    }
}