<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $address = $this->whenLoaded('address');

        return [
            'order_code'           => $this->id,
            'customer_name'        => optional($this->user)->full_name ?? 'N/A',
            'customer_email'       => optional($this->user)->email ?? 'N/A',
            'shipping_address'     => $address ? "{$address->detail}, {$address->ward}, {$address->district}, {$address->province}" : 'Không có địa chỉ',
            'customer_phone'       => optional($this->user)->phone ?? 'N/A',
            'order_date'           => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '--',
            'status'               => $this->status,
            'payment_method'       => $this->payment_method,
            'products'             => OrderItemResource::collection($this->whenLoaded('items')),
            'total_amount'         => number_format($this->total_amount, 0, ',', '.') . ' đ',
        ];
    }
}