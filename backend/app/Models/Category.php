<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;


class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'icon',
        'color',
        'display_order',
        'is_visible',
        'slug'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
{
    return $this->hasMany(Product::class)->where('status', 'active');
}

    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
    public function getAllChildIds(): array
    {
        $ids = [$this->id];
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildIds());
        }
        return $ids;
    }
    public function getBreadcrumb(): Collection
    {
        $breadcrumb = collect();
        $current = $this;
        while ($current) {
            $breadcrumb->prepend($current); // Thêm vào đầu collection
            $current = $current->parent;
        }
        return $breadcrumb;
    }
    public static function getAdminTree(): Collection
    {
        // 1. Lấy tất cả danh mục cùng với số lượng sản phẩm của mỗi danh mục
        // withCount sẽ tạo ra một thuộc tính 'products_count' cho mỗi category
        $allCategories = self::withCount('products')->get();

        // 2. Nhóm các danh mục theo parent_id để dễ dàng xây dựng cây
        $grouped = $allCategories->groupBy('parent_id');

        // 3. Lấy các danh mục gốc (có parent_id = null)
        $root = $grouped->get(null, collect());

        // 4. Xây dựng cây đệ quy
        self::buildTree($root, $grouped);

        return $root;
    }

    /**
     * Hàm đệ quy để gán các danh mục con vào danh mục cha.
     * @param \Illuminate\Support\Collection $categories
     * @param \Illuminate\Support\Collection $allGrouped
     */
    private static function buildTree(Collection $categories, Collection $allGrouped)
    {
        foreach ($categories as $category) {
            $children = $allGrouped->get($category->id, collect());
            $category->children = $children; // Gán collection con vào thuộc tính 'children'

            // Nếu có danh mục con, tiếp tục xây dựng cây cho chúng
            if ($children->isNotEmpty()) {
                self::buildTree($children, $allGrouped);
            }
        }
    }
    public function scopeTopFive(Builder $query): Builder
    {
        return $query->where('is_visible', true) // Chỉ lấy các danh mục đang được kích hoạt
            ->withCount('products')
            ->orderBy('display_order', 'asc') // Sắp xếp theo thứ tự hiển thị
            ->orderBy('id', 'asc') // Thêm sắp xếp phụ để đảm bảo thứ tự nhất quán khi display_order trùng nhau
            ->take(5); // Giới hạn chỉ lấy 5 danh mục
    }
}
