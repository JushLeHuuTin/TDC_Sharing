<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run()
    {
        // ⚠️ Cần xóa dữ liệu cũ trước khi chạy
        // ProductImage::truncate(); 

        $images = [
            // Product ID 1 (Giáo trình chuyên ngành số 1)
            ['product_id' => 1, 'image' => 'book-it-1.jpg', 'is_featured' => true],
            
            // Product ID 11 (Thiết bị học tập nâng cao 1) - Status Pending
            ['product_id' => 11, 'image' => 'headphone-case-1.jpg', 'is_featured' => true],
            
            // Product ID 21 (Phụ kiện / Tài liệu hỗ trợ 1)
            ['product_id' => 21, 'image' => 'accessory-box-1.jpg', 'is_featured' => true],
            ['product_id' => 21, 'image' => 'accessory-box-2.jpg', 'is_featured' => false],
            
            // Product ID 5 (Laptop Dell Inspiron 14 5402)
            ['product_id' => 5, 'image' => 'laptop-dell-used-1.jpg', 'is_featured' => true],
            ['product_id' => 5, 'image' => 'laptop-dell-used-2.jpg', 'is_featured' => false],
            ['product_id' => 5, 'image' => 'laptop-dell-used-3.jpg', 'is_featured' => false],
            
            // Product ID 12 (Thiết bị học tập nâng cao 2) - Status Sold
            ['product_id' => 12, 'image' => 'samsung-tablet-1.jpg', 'is_featured' => true],
            
            // Product ID 10 (Giáo trình chuyên ngành số 10)
            ['product_id' => 10, 'image' => 'math-book-1.jpg', 'is_featured' => true],
            
            // Product ID 19 (Laptop sinh viên 10)
            ['product_id' => 19, 'image' => 'laptop-hp-used-1.jpg', 'is_featured' => true],
        ];

        foreach ($images as $image) {
            ProductImage::create($image);
        }
    }
}