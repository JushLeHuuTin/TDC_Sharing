<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        $attributes = [
            // Attributes cho Điện thoại (category_id = 1)
            ['category_id' => 1, 'name' => 'RAM', 'data_type' => 'text'],
            ['category_id' => 1, 'name' => 'ROM', 'data_type' => 'text'],
            ['category_id' => 1, 'name' => 'Màn hình', 'data_type' => 'text'],
            ['category_id' => 1, 'name' => 'Camera', 'data_type' => 'text'],
            ['category_id' => 1, 'name' => 'Pin', 'data_type' => 'number'],
            ['category_id' => 1, 'name' => 'Màu sắc', 'data_type' => 'text'],
            
            // Attributes cho Laptop (category_id = 2)
            ['category_id' => 2, 'name' => 'CPU', 'data_type' => 'text'],
            ['category_id' => 2, 'name' => 'RAM', 'data_type' => 'text'],
            ['category_id' => 2, 'name' => 'Ổ cứng', 'data_type' => 'text'],
            ['category_id' => 2, 'name' => 'Card đồ họa', 'data_type' => 'text'],
            ['category_id' => 2, 'name' => 'Màn hình', 'data_type' => 'text'],
            ['category_id' => 2, 'name' => 'Trọng lượng', 'data_type' => 'number'],
            
            // Attributes cho Máy tính bảng (category_id = 3)
            ['category_id' => 3, 'name' => 'RAM', 'data_type' => 'text'],
            ['category_id' => 3, 'name' => 'ROM', 'data_type' => 'text'],
            ['category_id' => 3, 'name' => 'Màn hình', 'data_type' => 'text'],
            ['category_id' => 3, 'name' => 'Hỗ trợ bút', 'data_type' => 'boolean'],
            
            // Attributes cho Phụ kiện (category_id = 4)
            ['category_id' => 4, 'name' => 'Chất liệu', 'data_type' => 'text'],
            ['category_id' => 4, 'name' => 'Màu sắc', 'data_type' => 'text'],
            ['category_id' => 4, 'name' => 'Kích thước', 'data_type' => 'text'],
        ];

        foreach ($attributes as $attribute) {
            Attribute::create($attribute);
        }
    }
}
