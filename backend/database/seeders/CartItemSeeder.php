<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CartItem;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CartItemSeeder extends Seeder
{
    public function run()
    {
        $cartItems = [
            // Cart của user 2
            [
                'cart_item_id' => 'CART-' . strtoupper(Str::random(10)),
                'cart_id' => 1,
                'product_id' => 1, // iPhone 15 Pro Max
                'quantity' => 1,
                'price' => 29990000.00,
                'note' => 'Màu Titan Tự nhiên',
                'added_at' => Carbon::now()->subDays(2)
            ],
            [
                'cart_item_id' => 'CART-' . strtoupper(Str::random(10)),
                'cart_id' => 1,
                'product_id' => 9, // AirPods Pro
                'quantity' => 2,
                'price' => 6490000.00,
                'note' => null,
                'added_at' => Carbon::now()->subDays(1)
            ],
            
            // Cart của user 3
            [
                'cart_item_id' => 'CART-' . strtoupper(Str::random(10)),
                'cart_id' => 2,
                'product_id' => 5, // MacBook Pro
                'quantity' => 1,
                'price' => 52990000.00,
                'note' => 'Màu Bạc',
                'added_at' => Carbon::now()->subHours(12)
            ],
            [
                'cart_item_id' => 'CART-' . strtoupper(Str::random(10)),
                'cart_id' => 2,
                'product_id' => 11, // Anker PowerCore
                'quantity' => 3,
                'price' => 990000.00,
                'note' => null,
                'added_at' => Carbon::now()->subHours(6)
            ],
            
            // Cart của user 4
            [
                'cart_item_id' => 'CART-' . strtoupper(Str::random(10)),
                'cart_id' => 3,
                'product_id' => 7, // iPad Pro
                'quantity' => 1,
                'price' => 32990000.00,
                'note' => 'Bao gồm Apple Pencil',
                'added_at' => Carbon::now()->subDays(3)
            ],
        ];

        foreach ($cartItems as $item) {
            CartItem::create($item);
        }
    }
}
