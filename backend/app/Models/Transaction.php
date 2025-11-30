<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
      'transaction_id',      // Mã Order ID từ Momo (orderId)
        'order_id',            // FK, sẽ là NULL trong trường hợp Multi-Order
        'payment_method',
        'transaction_details', // JSON: chứa order_ids (array), momo_request_id, v.v.
        'status',
        'amount',
    ];

 protected $casts = [
        // Cast chi tiết giao dịch sang Array để lưu trữ/truy xuất mảng Order IDs
        'transaction_details' => 'array', 
        'amount' => 'decimal:2',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}