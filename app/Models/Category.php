<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id', 'name', 'description', 'icon',
        'color', 'display_order', 'is_visible', 'slug'
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
        return $this->hasMany(Product::class);
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
}