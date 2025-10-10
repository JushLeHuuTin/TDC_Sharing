<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Wishlist;

class WishlistSeeder extends Seeder
{
    public function run()
    {
        $wishlists = [
            ['user_id' => 2, 'product_id' => 3], // Samsung S24 Ultra
            ['user_id' => 2, 'product_id' => 5], // MacBook Pro
            ['user_id' => 2, 'product_id' => 7], // iPad Pro
            
            ['user_id' => 3, 'product_id' => 1], // iPhone 15 Pro Max
            ['user_id' => 3, 'product_id' => 4], // Samsung Z Fold
            ['user_id' => 3, 'product_id' => 10], // Sony WH
            
            ['user_id' => 4, 'product_id' => 5], // MacBook Pro
            ['user_id' => 4, 'product_id' => 9], // AirPods Pro
        ];

        foreach ($wishlists as $wishlist) {
            Wishlist::create($wishlist);
        }
    }
}