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
            ['user_id' => 2],
            ['user_id' => 3],
            ['user_id' => 4],
        ];

        foreach ($carts as $cart) {
            Cart::create($cart);
        }
    }
}