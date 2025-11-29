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
            'product_image' => optional($this->featuredImage)->image ?? 'https://placehold.co/600x400/EEE/31343C?text=No+Image', // Lấy ảnh đại diện, nếu không có thì dùng ảnh placeholder
            'status' => $this->status, // Frontend sẽ tự xử lý việc cắt ngắn "..."
            'description' => $this->description, 
            'views' => $this->views_count, 
            'title' => $this->title, 
            'price' => $this->price ? number_format($this->price, 0, ',', '.') . 'đ' : 'Liên hệ', // Format giá
            'seller_name' => optional($this->seller)->full_name ?? 'Người bán không khả dụng', // Lấy tên người bán, nếu bị xóa thì báo
            'university' => optional($this->seller)->university ?? 'Chưa cập nhật', // Lấy tên trường
            'created_date' => $this->created_at->format('d/m/Y'), // Format ngày tạo
        ];
    }
}