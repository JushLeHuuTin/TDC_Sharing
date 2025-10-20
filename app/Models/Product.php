<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
// Thêm các use statement khác nếu cần
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;


class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Tên bảng trong cơ sở dữ liệu.
     */
    protected $table = 'products';

    /**
     * Các thuộc tính có thể được gán hàng loạt (mass assignable).
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'stocks',
        'status',
        'category_id',
        'user_id',
        'slug',
        'views_count',
        'is_featured',
    ];

    /**
     * Mối quan hệ: Một sản phẩm có nhiều đánh giá (Review).
     * Đây là mối quan hệ chính xác cần có.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    /**
     * Mối quan hệ: Một sản phẩm thuộc về một người bán (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ... (Toàn bộ các hàm và mối quan hệ khác trong file Product.php của bạn có thể giữ nguyên) ...
     /**
     * Mối quan hệ: Một sản phẩm thuộc về một danh mục (Category).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Mối quan hệ: Một sản phẩm có nhiều hình ảnh (ProductImage).
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    
    /**
     * Mối quan hệ Many-to-Many: Lấy ra các Model Attribute thông qua bảng product_attributes.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes', 'product_id', 'attribute_id')
                    ->withPivot('value', 'id') // Lấy thêm cột 'value' và 'id' từ bảng trung gian
                    ->withTimestamps();
    }
}
