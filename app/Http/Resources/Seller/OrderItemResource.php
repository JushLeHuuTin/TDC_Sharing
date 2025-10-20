<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'product_name' => optional($this->product)->title ?? 'Sản phẩm không xác định',
            'price'        => number_format($this->price, 0, ',', '.') . ' đ',
            'quantity'     => $this->quantity,
            'subtotal'     => number_format($this->subtotal, 0, ',', '.') . ' đ',
        ];
    }
}
