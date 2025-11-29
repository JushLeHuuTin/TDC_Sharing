<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_code'     => $this->id, // Hoặc mã code riêng nếu có
            'customer_name'  => optional($this->user)->full_name ?? 'Khách hàng không xác định',
            'customer_phone' => optional($this->user)->phone ?? 'Không có', // Lấy từ user
            // Sửa lỗi Invalid Date: Trả về định dạng chuẩn ISO 8601 để JS dễ parse, hoặc format sẵn
            'created_at'     => $this->created_at, 
            'created_date'   => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
            // Sửa lỗi NaN đ: Đảm bảo trả về số hoặc chuỗi số hợp lệ
            'total_amount'   => $this->total_amount,
            'final_amount'   => number_format($this->total_amount, 0, ',', '.') . ' đ',
            'status'         => $this->status,
            'items_count'    => $this->orderItems->count(),
            // Eager load items nếu cần hiển thị tóm tắt
            'items'          => $this->whenLoaded('orderItems'),
        ];
    }
}