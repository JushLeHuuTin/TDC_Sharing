<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'order_code'       => $this->id,
            'customer_name'    => optional($this->user)->full_name ?? 'Khách vãng lai',
            'customer_email'   => optional($this->user)->email ?? 'N/A',
            'customer_phone'   => optional($this->user)->phone ?? 'N/A',
            'shipping_address' => $this->address ? ($this->address->specific_address . ', ' . $this->address->ward . ', ' . $this->address->district . ', ' . $this->address->province) : 'Tại cửa hàng',
            'order_date'       => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
            'payment_method'   => $this->payment_method,
            'status'           => $this->status,
            'total_amount'     => $this->total_amount,
            'final_amount'     => number_format($this->total_amount, 0, ',', '.') . ' đ',
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