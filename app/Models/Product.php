<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;
    // use SoftDeletes;
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
    // Hàm này PHẢI trả về đối tượng HasOne
    public function featuredImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_featured', true);
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
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where('status', 'active') // Chỉ tìm trong các sản phẩm đang hoạt động
                     ->with(['seller', 'featuredImage']) // Tải sẵn các quan hệ cần thiết
                     ->where(function (Builder $q) use ($keyword) {
                         // Tìm kiếm trong tên sản phẩm
                         $q->where('title', 'like', "%{$keyword}%")
                           // HOẶC tìm kiếm trong tên của người bán (qua quan hệ)
                           ->orWhereHas('seller', function (Builder $sellerQuery) use ($keyword) {
                               $sellerQuery->where('full_name', 'like', "%{$keyword}%"); 
                           });
                     })
                     ->latest(); // Sắp xếp kết quả mới nhất lên đầu
    }
    public function scopeActiveAndReady(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->with(['seller', 'featuredImage'])
            ->latest();
    }
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)
                     ->where('status', 'active')
                     ->with(['seller', 'featuredImage']) // Tải sẵn thông tin người bán VÀ trường đại học của họ
                     ->latest()
                     ->limit(4); // Giới hạn chỉ lấy 4 sản phẩm nổi bật
    }
    public function scopeInCategory(Builder $query, Category $category): Builder
    {
        $categoryIds = $category->getAllChildIds();

        return $query->whereIn('category_id', $categoryIds)
                     ->activeAndReady(); // Tái sử dụng scope đã có để lấy sản phẩm active và eager load
    }
}
