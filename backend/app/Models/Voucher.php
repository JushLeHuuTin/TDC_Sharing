<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value',
        'min_purchase', 'start_date', 'end_date', 'usage_limit', 'used_count'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isValid()
    {
        $now = now();
        return $this->start_date <= $now 
            && $this->end_date >= $now 
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }
}