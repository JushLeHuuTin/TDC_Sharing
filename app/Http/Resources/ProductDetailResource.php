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
            $formattedPhone = substr($phoneNumber, 0, 4) . '******' . substr($phoneNumber, -2);
        }

        return [
            'id' => $this->id,
            'title' => $this->title ?? 'Sản phẩm chưa đặt tên',
            'price' => $this->price > 0 ? number_format($this->price, 0, ',', '.') . 'đ' : 'Liên hệ',
            'description' => strip_tags($this->description), // Lọc thẻ HTML/XSS
            'images' => $this->whenLoaded('images', function () {
                // Trả về tối đa 5 ảnh
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
            'posted_time_ago' => $this->created_at ? $this->created_at->diffForHumans() : 'Không rõ thời gian',
            'views_count' => $this->views_count,
        ];
    }
}
