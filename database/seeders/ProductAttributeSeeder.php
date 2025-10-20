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
            ['product_id' => 1, 'attribute_id' => 1, 'value' => '8GB', 'value_int' => 8, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 1, 'attribute_id' => 2, 'value' => '256GB', 'value_int' => 256, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 1, 'attribute_id' => 3, 'value' => '6.7 inch Super Retina XDR', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 1, 'attribute_id' => 4, 'value' => '48MP chính + 12MP góc siêu rộng + 12MP tele', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 1, 'attribute_id' => 5, 'value' => '4422mAh', 'value_int' => 4422, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 1, 'attribute_id' => 6, 'value' => 'Titan Tự nhiên, Titan Xanh, Titan Trắng, Titan Đen', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            
            // iPhone 14 Pro (product_id = 2)
            ['product_id' => 2, 'attribute_id' => 1, 'value' => '6GB', 'value_int' => 6, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 2, 'attribute_id' => 2, 'value' => '128GB', 'value_int' => 128, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 2, 'attribute_id' => 3, 'value' => '6.1 inch Super Retina XDR', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 2, 'attribute_id' => 4, 'value' => '48MP chính + 12MP góc siêu rộng + 12MP tele', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 2, 'attribute_id' => 5, 'value' => '3200mAh', 'value_int' => 3200, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 2, 'attribute_id' => 6, 'value' => 'Tím, Vàng, Bạc, Đen', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            
            // Samsung S24 Ultra (product_id = 3)
            ['product_id' => 3, 'attribute_id' => 1, 'value' => '12GB', 'value_int' => 12, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 3, 'attribute_id' => 2, 'value' => '512GB', 'value_int' => 512, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 3, 'attribute_id' => 3, 'value' => '6.8 inch Dynamic AMOLED 2X', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 3, 'attribute_id' => 4, 'value' => '200MP chính + 50MP tele + 12MP góc siêu rộng + 10MP tele', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 3, 'attribute_id' => 5, 'value' => '5000mAh', 'value_int' => 5000, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 3, 'attribute_id' => 6, 'value' => 'Đen Titan, Xám, Tím, Vàng', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            
            // MacBook Pro (product_id = 5)
            ['product_id' => 5, 'attribute_id' => 7, 'value' => 'Apple M3 Pro (12 nhân)', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 5, 'attribute_id' => 8, 'value' => '18GB Unified Memory', 'value_int' => 18, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 5, 'attribute_id' => 9, 'value' => '512GB SSD', 'value_int' => 512, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 5, 'attribute_id' => 10, 'value' => 'Apple M3 Pro GPU 18 nhân', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 5, 'attribute_id' => 11, 'value' => '14.2 inch Liquid Retina XDR', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 5, 'attribute_id' => 12, 'value' => '1.6kg', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            
            // Dell XPS (product_id = 6)
            ['product_id' => 6, 'attribute_id' => 7, 'value' => 'Intel Core i7-13700H', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 6, 'attribute_id' => 8, 'value' => '16GB DDR5', 'value_int' => 16, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 6, 'attribute_id' => 9, 'value' => '512GB SSD NVMe', 'value_int' => 512, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 6, 'attribute_id' => 10, 'value' => 'NVIDIA RTX 4050 6GB', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 6, 'attribute_id' => 11, 'value' => '15.6 inch FHD+', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 6, 'attribute_id' => 12, 'value' => '1.8kg', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            
            // iPad Pro (product_id = 7)
            ['product_id' => 7, 'attribute_id' => 13, 'value' => '8GB', 'value_int' => 8, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 7, 'attribute_id' => 14, 'value' => '256GB', 'value_int' => 256, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 7, 'attribute_id' => 15, 'value' => '12.9 inch Liquid Retina XDR', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 7, 'attribute_id' => 16, 'value' => 'Có', 'value_int' => null, 'value_boolean' => true, 'value_date' => null],
            
            // AirPods Pro (product_id = 9)
            ['product_id' => 9, 'attribute_id' => 17, 'value' => 'Nhựa ABS cao cấp', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 9, 'attribute_id' => 18, 'value' => 'Trắng', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            
            // Sony WH (product_id = 10)
            ['product_id' => 10, 'attribute_id' => 17, 'value' => 'Nhựa và da PU', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
            ['product_id' => 10, 'attribute_id' => 18, 'value' => 'Đen, Bạc', 'value_int' => null, 'value_boolean' => null, 'value_date' => null],
        ];

        foreach ($attributes as $attribute) {
            ProductAttribute::create($attribute);
        }
    }
}