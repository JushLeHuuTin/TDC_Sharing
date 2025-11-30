<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id'      => $this->id,
            'order_code'    => $this->id, // Mã đơn (hiện tại dùng ID)
            
            // Thông tin
            'customer_name' => optional($this->user)->full_name ?? 'Khách vãng lai',
            'seller_name'   => optional($this->seller)->full_name ?? 'Seller ẩn danh',
            
            // Tiền & Ngày
            'total_price'   => number_format($this->total_amount ?? 0, 0, ',', '.') . ' đ',
            'order_date'    => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
            
            'status'        => $this->status,
            'items_count'   => $this->orderItems->count(), // Đếm số món
        ];
    }
}