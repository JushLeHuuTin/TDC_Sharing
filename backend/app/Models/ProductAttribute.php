<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
class ProductAttribute extends Pivot
{
    /**
     * Tên bảng trong cơ sở dữ liệu.
     * @var string
     */
    protected $table = 'product_attributes';

    /**
     * Bảng này có primary key tự tăng ('id').
     * @var bool
     */
    public $incrementing = true;
    
    /**
     * Bảng này không có timestamps (created_at, updated_at).
     * @var bool
     */
    public $timestamps = false;

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'attribute_id',
        'value',
        // Thêm các cột khác của bảng trung gian nếu có
    ];

    /**
     * Mối quan hệ: Một record ProductAttribute thuộc về một Attribute.
     */
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Mối quan hệ: Một record ProductAttribute thuộc về một Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}