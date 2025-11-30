<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Xử lý địa chỉ
        $shippingAddress = 'Tại cửa hàng';
        if ($this->address) {
             $shippingAddress = $this->address->specific_address . ', ' . $this->address->ward . ', ' . $this->address->district . ', ' . $this->address->province;
        } elseif ($this->address_id) {
             // Nếu chỉ có address_id mà không load được quan hệ, có thể hiển thị ID hoặc placeholder
             $shippingAddress = 'Địa chỉ ID: ' . $this->address_id;
        }

        return [
            'id'               => $this->id,
            'order_code'       => $this->id,
            
            // Khách hàng
            'customer_name'    => optional($this->user)->full_name ?? 'N/A',
            'customer_email'   => optional($this->user)->email ?? 'N/A',
            'customer_phone'   => optional($this->user)->phone ?? 'N/A',
            'shipping_address' => $shippingAddress,
            
            // Người bán (Admin cần biết)
            'seller_name'      => optional($this->seller)->full_name ?? 'N/A',
            
            // Chi tiết đơn
            'order_date'       => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
            'payment_method'   => $this->payment_method,
            'status'           => $this->status,
            'final_amount'     => number_format($this->total_amount, 0, ',', '.') . ' đ',

            // Danh sách sản phẩm
            'items'            => $this->orderItems->map(function($item) {
                return [
                    'id'           => $item->id,
                    'product_name' => optional($item->product)->title ?? 'Sản phẩm đã xóa',
                    'quantity'     => $item->quantity,
                    'price'        => $item->price,
                    'subtotal'     => $item->price * $item->quantity,
                ];
            }),
        ];
    }
}