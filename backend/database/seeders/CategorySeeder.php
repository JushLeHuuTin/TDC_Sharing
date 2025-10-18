<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'parent_id' => null,
                'name' => 'Điện thoại',
                'description' => 'Điện thoại di động các loại',
                'icon' => 'phone',
                'color' => '#FF6B6B',
                'display_order' => 1,
                'is_visible' => true,
                'slug' => 'dien-thoai'
            ],
            [
                'parent_id' => null,
                'name' => 'Laptop',
                'description' => 'Laptop, máy tính xách tay',
                'icon' => 'laptop',
                'color' => '#4ECDC4',
                'display_order' => 2,
                'is_visible' => true,
                'slug' => 'laptop'
            ],
            [
                'parent_id' => null,
                'name' => 'Máy tính bảng',
                'description' => 'Tablet các loại',
                'icon' => 'tablet',
                'color' => '#45B7D1',
                'display_order' => 3,
                'is_visible' => true,
                'slug' => 'may-tinh-bang'
            ],
            [
                'parent_id' => null,
                'name' => 'Phụ kiện',
                'description' => 'Phụ kiện điện thoại, laptop',
                'icon' => 'accessory',
                'color' => '#96CEB4',
                'display_order' => 4,
                'is_visible' => true,
                'slug' => 'phu-kien'
            ],
            [
                'parent_id' => 1,
                'name' => 'iPhone',
                'description' => 'Điện thoại iPhone',
                'icon' => 'apple',
                'color' => '#000000',
                'display_order' => 1,
                'is_visible' => true,
                'slug' => 'iphone'
            ],
            [
                'parent_id' => 1,
                'name' => 'Samsung',
                'description' => 'Điện thoại Samsung',
                'icon' => 'samsung',
                'color' => '#1428A0',
                'display_order' => 2,
                'is_visible' => true,
                'slug' => 'samsung'
            ],
            [
                'parent_id' => 4,
                'name' => 'Tai nghe',
                'description' => 'Tai nghe các loại',
                'icon' => 'headphone',
                'color' => '#FFD93D',
                'display_order' => 1,
                'is_visible' => true,
                'slug' => 'tai-nghe'
            ],
            [
                'parent_id' => 4,
                'name' => 'Sạc dự phòng',
                'description' => 'Pin sạc dự phòng',
                'icon' => 'battery',
                'color' => '#6BCB77',
                'display_order' => 2,
                'is_visible' => true,
                'slug' => 'sac-du-phong'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}