<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        $images = [
            // iPhone 15 Pro Max
            ['product_id' => 1, 'image' => 'iphone-15-pro-max-1.jpg', 'is_featured' => true],
            ['product_id' => 1, 'image' => 'iphone-15-pro-max-2.jpg', 'is_featured' => false],
            ['product_id' => 1, 'image' => 'iphone-15-pro-max-3.jpg', 'is_featured' => false],
            
            // iPhone 14 Pro
            ['product_id' => 2, 'image' => 'iphone-14-pro-1.jpg', 'is_featured' => true],
            ['product_id' => 2, 'image' => 'iphone-14-pro-2.jpg', 'is_featured' => false],
            
            // Samsung S24 Ultra
            ['product_id' => 3, 'image' => 'samsung-s24-ultra-1.jpg', 'is_featured' => true],
            ['product_id' => 3, 'image' => 'samsung-s24-ultra-2.jpg', 'is_featured' => false],
            ['product_id' => 3, 'image' => 'samsung-s24-ultra-3.jpg', 'is_featured' => false],
            
            // Samsung Z Fold 5
            ['product_id' => 4, 'image' => 'samsung-z-fold-5-1.jpg', 'is_featured' => true],
            ['product_id' => 4, 'image' => 'samsung-z-fold-5-2.jpg', 'is_featured' => false],
            
            // MacBook Pro
            ['product_id' => 5, 'image' => 'macbook-pro-14-m3-1.jpg', 'is_featured' => true],
            ['product_id' => 5, 'image' => 'macbook-pro-14-m3-2.jpg', 'is_featured' => false],
            
            // Dell XPS
            ['product_id' => 6, 'image' => 'dell-xps-15-1.jpg', 'is_featured' => true],
            
            // iPad Pro
            ['product_id' => 7, 'image' => 'ipad-pro-129-1.jpg', 'is_featured' => true],
            ['product_id' => 7, 'image' => 'ipad-pro-129-2.jpg', 'is_featured' => false],
            
            // Galaxy Tab
            ['product_id' => 8, 'image' => 'galaxy-tab-s9-1.jpg', 'is_featured' => true],
            
            // AirPods Pro
            ['product_id' => 9, 'image' => 'airpods-pro-2-1.jpg', 'is_featured' => true],
            
            // Sony WH
            ['product_id' => 10, 'image' => 'sony-wh-1000xm5-1.jpg', 'is_featured' => true],
            
            // Anker
            ['product_id' => 11, 'image' => 'anker-powercore-1.jpg', 'is_featured' => true],
            
            // Xiaomi
            ['product_id' => 12, 'image' => 'xiaomi-power-bank-1.jpg', 'is_featured' => true],
        ];

        foreach ($images as $image) {
            ProductImage::create($image);
        }
    }
}