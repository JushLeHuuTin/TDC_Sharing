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
        $review = $this->resource;

        return [
            'id'            => $review->id,
            
            // QUAN TRỌNG: Phải thêm dòng này để Frontend biết ai là chủ đánh giá
            'reviewer_id'   => $review->reviewer_id, 

            'reviewer_name' => optional($review->user)->full_name ?? 'Người dùng ẩn danh',
            
            // Thêm avatar nếu có (để hiển thị ảnh đại diện cho đẹp)
            'avatar'        => optional($review->user)->avatar ?? 'https://ui-avatars.io/api/?name=' . urlencode(optional($review->user)->full_name ?? 'User'),

            'rating'        => $review->rating,
            'comment'       => $review->comment,

            // SỬA LỖI DATE: Trả về nguyên gốc để Frontend tự format, tránh lỗi "Invalid Date"
            'created_at'    => $review->created_at, 
            
            // Nếu thích hiển thị kiểu "2 giờ trước", bạn có thể dùng thêm trường này
            'time_ago'      => $review->created_at ? $review->created_at->diffForHumans() : '--',
        ];
    }
}