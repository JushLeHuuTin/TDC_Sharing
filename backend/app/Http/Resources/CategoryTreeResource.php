<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTreeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? '(Không xác định)',
            'products_count' => $this->products_count ?? 0, // Lấy từ withCount
            'slug' => $this->slug, // Cung cấp slug để frontend tạo link sửa/xóa
            'color' => $this->color, // Cung cấp slug để frontend tạo link sửa/xóa
            'icon' => $this->icon, // Cung cấp slug để frontend tạo link sửa/xóa
            'total_products' => $this->total_products,
            'is_visible' => $this->is_visible,
            'description' => $this->description,
            'display_order' => $this->display_order,
            'updated_at' => $this->updated_at,
            // 'children' sẽ chỉ xuất hiện nếu collection con không rỗng
            'children' => self::collection($this->when(
                $this->children->isNotEmpty(),
                $this->children
            )),
        ];
    }
}