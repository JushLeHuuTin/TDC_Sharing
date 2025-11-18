<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $orders = [
            [
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => 2,
                'buyer_id' => 2,
                'payment_method' => 'bank_transfer',
                'address_id' => 1,
                'voucher_id' => 1,
                'status' => 'delivered',
                'total_amount' => 35480000.00,
                'discount_amount' => 3548000.00,
                'final_amount' => 31932000.00
            ],
            [
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => 3,
                'buyer_id' => 3,
                'payment_method' => 'credit_card',
                'address_id' => 3,
                'voucher_id' => 2,
                'status' => 'shipped',
                'total_amount' => 24990000.00,
                'discount_amount' => 500000.00,
                'final_amount' => 24490000.00
            ],
            [
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => 2,
                'buyer_id' => 2,
                'payment_method' => 'cod',
                'address_id' => 2,
                'voucher_id' => null,
                'status' => 'processing',
                'total_amount' => 8990000.00,
                'discount_amount' => 0.00,
                'final_amount' => 8990000.00
            ],
            [
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => 4,
                'buyer_id' => 4,
                'payment_method' => 'e_wallet',
                'address_id' => 4,
                'voucher_id' => 3,
                'status' => 'pending',
                'total_amount' => 1390000.00,
                'discount_amount' => 50000.00,
                'final_amount' => 1340000.00
            ],
            [
                'order_id' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => 3,
                'buyer_id' => 3,
                'payment_method' => 'bank_transfer',
                'address_id' => 3,
                'voucher_id' => null,
                'status' => 'cancelled',
                'total_amount' => 45990000.00,
                'discount_amount' => 0.00,
                'final_amount' => 45990000.00
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
