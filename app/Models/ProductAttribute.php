<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
/**
 * @property int $id
 * @property int $product_id
 * @property int $attribute_id
 * @property string $value
 * @property int|null $value_int
 * @property int|null $value_boolean
 * @property string|null $value_date
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Attribute $attribute
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereAttributeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereValueBoolean($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereValueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductAttribute whereValueInt($value)
 * @mixin \Eloquent
 */
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