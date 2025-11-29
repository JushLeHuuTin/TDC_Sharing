<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Models\Category; // Cần thiết để lấy ID nếu cần, nhưng tạm dùng ID cứng

class AttributeSeeder extends Seeder
{
    public function run()
    {
        // ⚠️ Tạm thời xóa dữ liệu cũ để tránh ID bị sai lệch
        // Attribute::truncate(); 
        
        $attributes = [
            // --- CẤP 2: Giáo trình & Sách chuyên ngành (category_id = 5) ---
            [
                'category_id' => 5, 
                'name' => 'Tình trạng sách', 
                'data_type' => 'select', 
                
            ],
            [
                'category_id' => 5, 
                'name' => 'Tác giả', 
                'data_type' => 'TEXT', 
                
            ],
            [
                'category_id' => 5, 
                'name' => 'Năm xuất bản', 
                'data_type' => 'NUMBER', 
                
            ],
            
            // --- CẤP 2: Đề thi & Bài tập (category_id = 6) ---
            [
                'category_id' => 6, 
                'name' => 'Dạng file', 
                'data_type' => 'select', 
                
            ],
            [
                'category_id' => 6, 
                'name' => 'Số lượng đề', 
                'data_type' => 'NUMBER', 
                
            ],
            
            // --- CẤP 2: Laptop, PC & Linh kiện (category_id = 7) ---
            [
                'category_id' => 7, 
                'name' => 'Tình trạng', 
                'data_type' => 'select', 
                
            ],
            [
                'category_id' => 7, 
                'name' => 'RAM', 
                'data_type' => 'TEXT', 
                
            ],
            [
                'category_id' => 7, 
                'name' => 'Ổ cứng', 
                'data_type' => 'TEXT', 
                
            ],
            [
                'category_id' => 7, 
                'name' => 'Bảo hành còn', 
                'data_type' => 'TEXT', 
                
            ],

            // --- CẤP 2: Phụ kiện học tập (category_id = 8) ---
            [
                'category_id' => 8, 
                'name' => 'Loại phụ kiện', 
                'data_type' => 'TEXT', 
                
            ],
            [
                'category_id' => 8, 
                'name' => 'Thương hiệu', 
                'data_type' => 'TEXT', 
                
            ],
        ];

        foreach ($attributes as $attribute) {
            Attribute::updateOrCreate(
                ['category_id' => $attribute['category_id'], 'name' => $attribute['name']],
                $attribute
            );
        }
    }
}