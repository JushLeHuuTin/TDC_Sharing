<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $category_id
 * @property string $title
 * @property string|null $description
 * @property numeric $price
 * @property string $status
 * @property int $stocks
 * @property int $is_visible
 * @property bool $is_featured
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attribute> $attributes
 * @property-read int|null $attributes_count
 * @property-read \App\Models\Category $category
 * @property-read string|null $featured_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductImage> $images
 * @property-read int|null $images_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ProductAttribute> $productAttributes
 * @property-read int|null $product_attributes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $wishlistedByUsers
 * @property-read int|null $wishlisted_by_users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStocks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
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