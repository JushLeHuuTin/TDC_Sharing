<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            // Lấy tên người dùng, nếu không có (VD: user đã bị xóa) thì trả về 'Người dùng ẩn danh'
            'reviewer_name'  => $this->whenLoaded('user', optional($this->user)->full_name ?? 'Người dùng ẩn danh'),
            'rating'         => $this->rating,
            'comment'        => $this->comment,
            // Định dạng ngày tháng cho dễ đọc (VD: "2 ngày trước")
            'created_at'     => $this->created_at ? $this->created_at->diffForHumans() : '--',
        ];
    }
}