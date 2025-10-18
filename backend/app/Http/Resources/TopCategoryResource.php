<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name ?? 'Danh mục chưa có tên', // Fallback nếu tên null
            'slug' => $this->slug, // Cung cấp slug để frontend tạo link
            'icon' => $this->icon ?? 'fas fa-tag', // Icon mặc định nếu không có
            'color' => $this->color ?? '#333333', // Màu mặc định nếu không có
        ];
    }
}