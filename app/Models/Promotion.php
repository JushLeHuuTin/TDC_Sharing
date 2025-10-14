<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'discount_type', 'discount_value',
        'min_purchase', 'max_discount', 'start_date', 'end_date',
        'usage_limit', 'per_customer_limit', 'is_active',
        'applicable_products', 'applicable_categories'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];
}