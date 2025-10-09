<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'attribute_id', 'value',
        'value_int', 'value_boolean', 'value_date'
    ];

    protected $casts = [
        'value_boolean' => 'boolean',
        'value_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
