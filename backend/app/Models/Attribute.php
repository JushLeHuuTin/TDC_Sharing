<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    protected $table = 'attributes';

    /**
     * Các thuộc tính có thể được gán hàng loạt.
     */
    protected $fillable = [
        'name',
        'category_id', // Thêm category_id vào đây
        'data_type',
    ];

    //======================================================================
    // MỐI QUAN HỆ (RELATIONSHIPS)
    //======================================================================

    /**
     * Mối quan hệ: Một thuộc tính (Attribute) thuộc về một danh mục (Category).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Mối quan hệ: Một thuộc tính có thể có nhiều giá trị sản phẩm.
     */
    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Mối quan hệ Many-to-Many: Lấy ra các sản phẩm (Product) có thuộc tính này.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_attributes', 'attribute_id', 'product_id')
                    ->withPivot('value', 'id')
                    ->withTimestamps();
    }
    public function attributesOptions()
    {
        return $this->hasMany(AttributeOptions::class);
    }
}
