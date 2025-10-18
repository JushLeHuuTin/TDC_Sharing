<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'user_id' => 1,
                'category_id' => 5, // iPhone
                'title' => 'iPhone 15 Pro Max 256GB',
                'description' => 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP, màn hình Super Retina XDR 6.7 inch',
                'price' => 29990000.00,
                'status' => 'active',
                'stocks' => 50,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 0,
                'slug' => 'iphone-15-pro-max-256gb'
            ],
            [
                'user_id' => 1,
                'category_id' => 5,
                'title' => 'iPhone 14 Pro 128GB',
                'description' => 'iPhone 14 Pro với Dynamic Island, camera 48MP, chip A16 Bionic',
                'price' => 24990000.00,
                'status' => 'active',
                'stocks' => 30,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 0,
                'slug' => 'iphone-14-pro-128gb'
            ],
            [
                'user_id' => 1,
                'category_id' => 6, // Samsung
                'title' => 'Samsung Galaxy S24 Ultra 512GB',
                'description' => 'Galaxy S24 Ultra với bút S Pen, camera 200MP, màn hình Dynamic AMOLED 6.8 inch',
                'price' => 31990000.00,
                'status' => 'active',
                'stocks' => 40,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 0,
                'slug' => 'samsung-galaxy-s24-ultra-512gb'
            ],
            [
                'user_id' => 1,
                'category_id' => 6,
                'title' => 'Samsung Galaxy Z Fold 5 256GB',
                'description' => 'Điện thoại màn hình gập Galaxy Z Fold 5, chip Snapdragon 8 Gen 2',
                'price' => 35990000.00,
                'status' => 'active',
                'stocks' => 15,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 0,
                'slug' => 'samsung-galaxy-z-fold-5-256gb'
            ],
            [
                'user_id' => 1,
                'category_id' => 2, // Laptop
                'title' => 'MacBook Pro 14 inch M3 Pro',
                'description' => 'MacBook Pro 14 inch với chip M3 Pro, RAM 18GB, SSD 512GB, màn hình Liquid Retina XDR',
                'price' => 52990000.00,
                'status' => 'active',
                'stocks' => 25,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 0,
                'slug' => 'macbook-pro-14-m3-pro'
            ],
            [
                'user_id' => 1,
                'category_id' => 2,
                'title' => 'Dell XPS 15 9530',
                'description' => 'Dell XPS 15 với Intel Core i7-13700H, RAM 16GB, SSD 512GB, RTX 4050',
                'price' => 45990000.00,
                'status' => 'active',
                'stocks' => 20,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 0,
                'slug' => 'dell-xps-15-9530'
            ],
            [
                'user_id' => 1,
                'category_id' => 3, // Tablet
                'title' => 'iPad Pro 12.9 inch M2 256GB',
                'description' => 'iPad Pro với chip M2, màn hình Liquid Retina XDR, hỗ trợ Apple Pencil 2',
                'price' => 32990000.00,
                'status' => 'active',
                'stocks' => 35,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 0,
                'slug' => 'ipad-pro-129-m2-256gb'
            ],
            [
                'user_id' => 1,
                'category_id' => 3,
                'title' => 'Samsung Galaxy Tab S9 Ultra',
                'description' => 'Galaxy Tab S9 Ultra với màn hình 14.6 inch, chip Snapdragon 8 Gen 2, bút S Pen',
                'price' => 28990000.00,
                'status' => 'active',
                'stocks' => 18,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 0,
                'slug' => 'samsung-galaxy-tab-s9-ultra'
            ],
            [
                'user_id' => 1,
                'category_id' => 7, // Tai nghe
                'title' => 'AirPods Pro 2 USB-C',
                'description' => 'Tai nghe AirPods Pro thế hệ 2 với chip H2, chống ồn chủ động, cổng USB-C',
                'price' => 6490000.00,
                'status' => 'active',
                'stocks' => 100,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 0,
                'slug' => 'airpods-pro-2-usb-c'
            ],
            [
                'user_id' => 1,
                'category_id' => 7,
                'title' => 'Sony WH-1000XM5',
                'description' => 'Tai nghe Sony WH-1000XM5 chống ồn cao cấp, âm thanh Hi-Res',
                'price' => 8990000.00,
                'status' => 'active',
                'stocks' => 60,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 0,
                'slug' => 'sony-wh-1000xm5'
            ],
            [
                'user_id' => 1,
                'category_id' => 8, // Sạc dự phòng
                'title' => 'Anker PowerCore 20000mAh',
                'description' => 'Pin sạc dự phòng Anker 20000mAh, sạc nhanh PD 20W',
                'price' => 990000.00,
                'status' => 'active',
                'stocks' => 200,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 0,
                'slug' => 'anker-powercore-20000mah'
            ],
            [
                'user_id' => 1,
                'category_id' => 8,
                'title' => 'Xiaomi Power Bank 3 10000mAh',
                'description' => 'Pin sạc dự phòng Xiaomi 10000mAh, nhỏ gọn, sạc nhanh 18W',
                'price' => 390000.00,
                'status' => 'active',
                'stocks' => 300,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 0,
                'slug' => 'xiaomi-power-bank-3-10000mah'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
