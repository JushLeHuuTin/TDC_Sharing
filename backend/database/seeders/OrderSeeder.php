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
                'user_id' => 2,
                'payment_method' => 'bank_transfer',
                'address_id' => 1,
                'voucher_id' => 1,
                'status' => 'delivered',
                'total_amount' => 35480000.00,
            ],
            [
                'user_id' => 3,
                'payment_method' => 'credit_card',
                'address_id' => 3,
                'voucher_id' => 2,
                'status' => 'shipped',
                'total_amount' => 24990000.00,
            ],
            [
                'user_id' => 2,
                'payment_method' => 'cod',
                'address_id' => 2,
                'voucher_id' => null,
                'status' => 'processing',
                'total_amount' => 8990000.00,
            ],
            [
                'user_id' => 4,
                'payment_method' => 'e_wallet',
                'address_id' => 4,
                'voucher_id' => 3,
                'status' => 'pending',
                'total_amount' => 1390000.00,
            ],
            [
                'user_id' => 3,
                'payment_method' => 'bank_transfer',
                'address_id' => 3,
                'voucher_id' => null,
                'status' => 'cancelled',
                'total_amount' => 45990000.00,
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
}
