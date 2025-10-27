<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cart;

class CartSeeder extends Seeder
{
    public function run()
    {
        $carts = [
            ['user_id' => 2, 'seller_id' => 3],
            ['user_id' => 3, "seller_id" => 2],
            ['user_id' => 4, "seller_id" => 1],
            ['user_id' => 2, "seller_id" => 1],
        ];

        foreach ($carts as $cart) {
            Cart::create($cart);
        }
    }
}
