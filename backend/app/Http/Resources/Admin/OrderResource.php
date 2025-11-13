<?php

namespace App\Http\Resources\Admin;

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
        // Lấy danh sách tên người bán duy nhất từ các sản phẩm trong đơn hàng
        $sellerNames = $this->whenLoaded('items.product.seller', function () {
            return $this->items->map(function ($item) {
                // SỬA LỖI: Dùng optional() để truy cập an toàn, tránh lỗi khi seller bị null
                return optional(optional($item->product)->seller)->full_name ?? 'N/A';
            })->unique()->implode(', ');
        });

        // Lấy danh sách tên sản phẩm
        $productNames = $this->whenLoaded('items.product', function () {
            return $this->items->map(function ($item) {
                 // SỬA LỖI: Dùng optional() để truy cập an toàn
                return optional($item->product)->title ?? 'Sản phẩm không xác định';
            })->implode(', ');
        });

        return [
            'order_id'      => $this->id,
            'customer_name' => $this->whenLoaded('buyer', $this->buyer->full_name ?? 'Khách hàng không xác định'),
            'seller_name'   => $sellerNames,
            'products'      => $productNames,
            'total_price'   => number_format($this->final_amount, 0, ',', '.') . ' đ',
            'status'        => $this->status,
            'order_date'    => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
        ];
    }
}