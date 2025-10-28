<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // Sử dụng $this->resource để rõ ràng hơn
        $review = $this->resource; 

        return [
            'id'            => $review->id,
            // Đảm bảo truy cập đúng $review->user
            'reviewer_name' => optional($review->user)->full_name ?? 'Người dùng ẩn danh', 
            'rating'        => $review->rating,
            'comment'       => $review->comment,
            'created_at'    => $review->created_at ? $review->created_at->diffForHumans() : '--',
        ];
    }
}