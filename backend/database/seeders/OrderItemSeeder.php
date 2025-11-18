<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderItemSeeder extends Seeder
{
    public function run()
    {
        $orderItems = [
            // Order 1
            [
                'order_item_id' => 'ORDITEM-' . strtoupper(Str::random(10)),
                'order_id' => 1,
                'product_id' => 1, // iPhone 15 Pro Max
                'quantity' => 1,
                'price' => 29990000.00,
                'subtotal' => 29990000.00
            ],
            [
                'order_item_id' => 'ORDITEM-' . strtoupper(Str::random(10)),
                'order_id' => 1,
                'product_id' => 9, // AirPods Pro
                'quantity' => 1,
                'price' => 6490000.00,
                'subtotal' => 6490000.00
            ],
            
            // Order 2
            [
                'order_item_id' => 'ORDITEM-' . strtoupper(Str::random(10)),
                'order_id' => 2,
                'product_id' => 2, // iPhone 14 Pro
                'quantity' => 1,
                'price' => 24990000.00,
                'subtotal' => 24990000.00
            ],
            
            // Order 3
            [
                'order_item_id' => 'ORDITEM-' . strtoupper(Str::random(10)),
                'order_id' => 3,
                'product_id' => 10, // Sony WH
                'quantity' => 1,
                'price' => 8990000.00,
                'subtotal' => 8990000.00
            ],
            
            // Order 4
            [
                'order_item_id' => 'ORDITEM-' . strtoupper(Str::random(10)),
                'order_id' => 4,
                'product_id' => 11, // Anker PowerCore
                'quantity' => 1,
                'price' => 990000.00,
                'subtotal' => 990000.00
            ],
            [
                'order_item_id' => 'ORDITEM-' . strtoupper(Str::random(10)),
                'order_id' => 4,
                'product_id' => 12, // Xiaomi Power Bank
                'quantity' => 1,
                'price' => 390000.00,
                'subtotal' => 390000.00
            ],
            
            // Order 5
            [
                'order_item_id' => 'ORDITEM-' . strtoupper(Str::random(10)),
                'order_id' => 5,
                'product_id' => 6, // Dell XPS
                'quantity' => 1,
                'price' => 45990000.00,
                'subtotal' => 45990000.00
            ],
        ];

        foreach ($orderItems as $item) {
            OrderItem::create($item);
        }
    }
}