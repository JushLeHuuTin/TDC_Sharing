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
            'order_code'     => $this->id,
            'customer_name'  => $this->whenLoaded('buyer', $this->buyer->full_name ?? 'Người dùng ẩn danh'),
            'customer_phone' => $this->whenLoaded('buyer', $this->buyer->phone ?? 'Không có'),
            'created_date'   => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
            'final_amount'   => number_format($this->final_amount ?? 0, 0, ',', '.') . ' đ',
            'status'         => $this->status ?? 'Không xác định',
        ];
    }
}