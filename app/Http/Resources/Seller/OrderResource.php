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
            'order_code'    => $this->order_id, // Lấy từ cột order_id
            'customer_name' => optional($this->buyer)->full_name ?? 'Người dùng ẩn danh',
            'customer_phone'=> optional($this->buyer)->phone ?? 'Không có',
            'order_date'    => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
            'total_price'   => number_format($this->final_amount, 0, ',', '.') . ' đ',
            'status'        => $this->status,
        ];
    }
}