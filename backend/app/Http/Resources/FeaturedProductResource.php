<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str; 
class FeaturedProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'product_image' => $this->featuredImage?->image ?? 'https://placehold.co/600x400?text=Image',
            'title' => Str::limit($this->title ?? 'Sản phẩm chưa đặt tên', 50, '...'),
            'price' => $this->price > 0 ? number_format($this->price, 0, ',', '.') . 'đ' : 'Liên hệ',
            'seller_name' => Str::limit($this->seller?->full_name ?? 'Người bán không xác định', 30, '...'),
            'university' => $this->seller?->university?->name ?? 'Chưa cập nhật', // Dùng nullsafe operator cho nested relation
            'created_date' => $this->created_at ? $this->created_at->format('d/m/Y') : 'Không rõ ngày',
        ];
    }
}