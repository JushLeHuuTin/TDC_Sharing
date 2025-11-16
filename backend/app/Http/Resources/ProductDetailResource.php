<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Xử lý logic ẩn số điện thoại
        $phoneNumber = $this->seller?->phone;
        $formattedPhone = 'Người bán chưa cung cấp số điện thoại.';
        if ($phoneNumber && strlen($phoneNumber) >= 10) {
            $formattedPhone = $phoneNumber;
        }
    
        // Convert attributes → associative array
        $attributes = $this->whenLoaded('attributes', function () {
            return $this->attributes->mapWithKeys(function ($attr) {
                return [
                    $attr->name => $attr->pivot->value,
                ];
            });
        }, collect());
    
        // Chia đôi specs
        $half = ceil($attributes->count() / 2);
        $specsLeft  = $attributes->slice(0, $half);
        $specsRight = $attributes->slice($half);
    
        return [
            'id' => $this->id,
            'title' => $this->title ?? 'Sản phẩm chưa đặt tên',
            'price' => $this->price > 0 ? number_format($this->price, 0, ',', '.') . 'đ' : 'Liên hệ',
            'description' => strip_tags($this->description),
    
            'images' => $this->whenLoaded('images', function () {
                return $this->images->take(5)->map(fn($image) => [
                    'path' => $image->image,
                    'is_featured' => (bool)$image->is_featured,
                ]);
            }, ['https://placehold.co/600x400?text=No+Image']),
    
            'seller' => [
                'name' => $this->seller?->full_name ?? 'Người bán không xác định',
                'university' => $this->seller?->university?->name ?? 'Chưa cập nhật',
                'phone' => $formattedPhone,
            ],
    
            // ⚡ Trả về đúng format Vue cần
            'specsLeft'  => $specsLeft,
            'specsRight' => $specsRight,
    
            'posted_time_ago' => $this->created_at ? $this->created_at->diffForHumans() : 'Không rõ thời gian',
            'views_count' => $this->views_count,
        ];
    }
}
