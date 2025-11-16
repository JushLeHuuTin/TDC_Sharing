<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;

class ProductAttributeSeeder extends Seeder
{
    public function run()
    {
        $attributes = [
            // iPhone 15 Pro Max (product_id = 1)
            ['product_id' => 1, 'attribute_id' => 1, 'value' => '8GB'],
            ['product_id' => 1, 'attribute_id' => 2, 'value' => '256GB'],
            ['product_id' => 1, 'attribute_id' => 3, 'value' => '6.7 inch Super Retina XDR'],
            ['product_id' => 1, 'attribute_id' => 4, 'value' => '48MP chính + 12MP góc siêu rộng + 12MP tele'],
            ['product_id' => 1, 'attribute_id' => 5, 'value' => '4422mAh'],
            ['product_id' => 1, 'attribute_id' => 6, 'value' => 'Titan Tự nhiên, Titan Xanh, Titan Trắng, Titan Đen'],
            
            // iPhone 14 Pro (product_id = 2)
            ['product_id' => 2, 'attribute_id' => 1, 'value' => '6GB'],
            ['product_id' => 2, 'attribute_id' => 2, 'value' => '128GB'],
            ['product_id' => 2, 'attribute_id' => 3, 'value' => '6.1 inch Super Retina XDR'],
            ['product_id' => 2, 'attribute_id' => 4, 'value' => '48MP chính + 12MP góc siêu rộng + 12MP tele'],
            ['product_id' => 2, 'attribute_id' => 5, 'value' => '3200mAh'],
            ['product_id' => 2, 'attribute_id' => 6, 'value' => 'Tím, Vàng, Bạc, Đen'],
            
            // Samsung S24 Ultra (product_id = 3)
            ['product_id' => 3, 'attribute_id' => 1, 'value' => '12GB'],
            ['product_id' => 3, 'attribute_id' => 2, 'value' => '512GB'],
            ['product_id' => 3, 'attribute_id' => 3, 'value' => '6.8 inch Dynamic AMOLED 2X'],
            ['product_id' => 3, 'attribute_id' => 4, 'value' => '200MP chính + 50MP tele + 12MP góc siêu rộng + 10MP tele'],
            ['product_id' => 3, 'attribute_id' => 5, 'value' => '5000mAh'],
            ['product_id' => 3, 'attribute_id' => 6, 'value' => 'Đen Titan, Xám, Tím, Vàng'],
            
            // MacBook Pro (product_id = 5)
            ['product_id' => 5, 'attribute_id' => 7, 'value' => 'Apple M3 Pro (12 nhân)'],
            ['product_id' => 5, 'attribute_id' => 8, 'value' => '18GB Unified Memory'],
            ['product_id' => 5, 'attribute_id' => 9, 'value' => '512GB SSD'],
            ['product_id' => 5, 'attribute_id' => 10, 'value' => 'Apple M3 Pro GPU 18 nhân'],
            ['product_id' => 5, 'attribute_id' => 11, 'value' => '14.2 inch Liquid Retina XDR'],
            ['product_id' => 5, 'attribute_id' => 12, 'value' => '1.6kg'],
            
            // Dell XPS (product_id = 6)
            ['product_id' => 6, 'attribute_id' => 7, 'value' => 'Intel Core i7-13700H'],
            ['product_id' => 6, 'attribute_id' => 8, 'value' => '16GB DDR5'],
            ['product_id' => 6, 'attribute_id' => 9, 'value' => '512GB SSD NVMe'],
            ['product_id' => 6, 'attribute_id' => 10, 'value' => 'NVIDIA RTX 4050 6GB'],
            ['product_id' => 6, 'attribute_id' => 11, 'value' => '15.6 inch FHD+'],
            ['product_id' => 6, 'attribute_id' => 12, 'value' => '1.8kg'],
            
            // iPad Pro (product_id = 7)
            ['product_id' => 7, 'attribute_id' => 13, 'value' => '8GB'],
            ['product_id' => 7, 'attribute_id' => 14, 'value' => '256GB'],
            ['product_id' => 7, 'attribute_id' => 15, 'value' => '12.9 inch Liquid Retina XDR'],
            ['product_id' => 7, 'attribute_id' => 16, 'value' => 'Có'],
            
            // AirPods Pro (product_id = 9)
            ['product_id' => 9, 'attribute_id' => 17, 'value' => 'Nhựa ABS cao cấp'],
            ['product_id' => 9, 'attribute_id' => 18, 'value' => 'Trắng'],
            
            // Sony WH (product_id = 10)
            ['product_id' => 10, 'attribute_id' => 17, 'value' => 'Nhựa và da PU'],
            ['product_id' => 10, 'attribute_id' => 18, 'value' => 'Đen, Bạc'],
        ];

        foreach ($attributes as $attribute) {
            ProductAttribute::create($attribute);
        }
    }
}