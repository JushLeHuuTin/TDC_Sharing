<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 'order_id', 'payment_method',
        'transaction_details', 'status'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
