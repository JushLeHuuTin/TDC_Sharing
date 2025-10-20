<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
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
     * Các thuộc tính cần được cast sang kiểu dữ liệu gốc.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'stocks' => 'integer',
    ];

    //======================================================================
    // MỐI QUAN HỆ (RELATIONSHIPS)
    //======================================================================

    /**
     * Mối quan hệ: Một sản phẩm thuộc về một người bán (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * SỬA LỖI: Thêm mối quan hệ `seller()` để tương thích với logic hiện tại.
     * Đây thực chất là một tên gọi khác cho mối quan hệ `user()`.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
     * Mối quan hệ: Một sản phẩm có nhiều đánh giá (Review).
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    /**
     * Mối quan hệ Many-to-Many: Lấy ra các Model Attribute thông qua bảng product_attributes.
     * Quan trọng: withPivot('value') để lấy được giá trị của thuộc tính đó.
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes', 'product_id', 'attribute_id')
                    ->withPivot('value', 'id') // Lấy thêm cột 'value' và 'id' từ bảng trung gian
                    ->withTimestamps();
    }
    public function productAttributes(): HasMany
    {
        // Giả sử bạn có model App\Models\ProductAttribute cho bảng product_attributes
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }
    /**
     * Mối quan hệ Many-to-Many: Lấy ra những người dùng đã thêm sản phẩm này vào Wishlist.
     */
    public function wishlistedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlist', 'product_id', 'user_id')
                    ->withTimestamps();
    }

    //======================================================================
    // ACCESSORS (TRUY CẬP THUỘC TÍNH)
    //======================================================================

    /**
     * Accessor: Lấy URL đầy đủ của ảnh đại diện.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        $featuredImage = $this->images()->where('is_featured', true)->first();

        if (!$featuredImage) {
            $featuredImage = $this->images()->first();
        }

        if ($featuredImage && $featuredImage->url) {
            // Kiểm tra xem URL đã là URL đầy đủ chưa
            if (filter_var($featuredImage->url, FILTER_VALIDATE_URL)) {
                return $featuredImage->url;
            }
            // Nếu chỉ là path, dùng Storage::url()
            return Storage::url($featuredImage->url);
        }

        // Trả về ảnh mặc định nếu không có ảnh nào
        return asset('images/default-product.png');
    }

    //======================================================================
    // QUERY SCOPES (PHẠM VI TRUY VẤN)
    //======================================================================

    /**
     * Scope: Chỉ lấy các sản phẩm đang được "published".
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }
}
