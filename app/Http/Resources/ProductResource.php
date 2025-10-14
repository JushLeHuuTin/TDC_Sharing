<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'product_image' => optional($this->featuredImage)->image_path ?? 'https://placehold.co/600x400/EEE/31343C?text=No+Image', // Lấy ảnh đại diện, nếu không có thì dùng ảnh placeholder
            'title' => $this->name, // Frontend sẽ tự xử lý việc cắt ngắn "..."
            'price' => $this->price ? number_format($this->price, 0, ',', '.') . 'đ' : 'Liên hệ', // Format giá
            'seller_name' => optional($this->seller)->name ?? 'Người bán không khả dụng', // Lấy tên người bán, nếu bị xóa thì báo
            'university' => optional($this->seller)->university ?? 'Chưa cập nhật', // Lấy tên trường
            'created_date' => $this->created_at->format('d/m/Y'), // Format ngày tạo
        ];
    }
}