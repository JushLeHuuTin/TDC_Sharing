<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductImage;
use Illuminate\Support\Str; // Import Str để tạo tên file giả

class ProductImageSeeder extends Seeder
{
    /**
     * Chạy product image seeds.
     * Cấu trúc lưu trữ: products/{product_id}/{hashed_filename}.jpg
     */
    public function run()
    {
        // 1. Xóa toàn bộ dữ liệu cũ
        ProductImage::truncate(); 

        $images = [];

        // Hàm tạo đường dẫn ảnh giả lập
        $makeImagePath = function ($productId, $description) {
            // Sử dụng Product ID làm tên thư mục
            $folder = $productId;
            // Tạo tên file ngẫu nhiên 
            $fileName = Str::random(40) . '.jpg';
            // Đường dẫn lưu vào DB (sử dụng /products/ để đồng bộ với disk public/products)
            return "products/{$folder}/{$fileName}";
        };

        // --- USER 1 PRODUCTS (ID 1-40) ---

        // 1. Product ID 1 (Giáo trình chuyên ngành số 1)
        $images[] = [
            'product_id' => 1, 
            'image' => $makeImagePath(1, 'Giao_trinh_01'), 
            'is_featured' => true
        ];
        
        // 2. Product ID 5 (Giáo trình chuyên ngành số 5)
        $images[] = [
            'product_id' => 5, 
            'image' => $makeImagePath(5, 'Giao_trinh_05_featured'), 
            'is_featured' => true
        ];
        $images[] = [
            'product_id' => 5, 
            'image' => $makeImagePath(5, 'Giao_trinh_05_detail'), 
            'is_featured' => false
        ];
        
        // 3. Product ID 15 (Laptop sinh viên, thuộc nhóm ID 11-19)
        $images[] = [
            'product_id' => 15, 
            'image' => $makeImagePath(15, 'Laptop_sv_main'), 
            'is_featured' => true
        ];
        $images[] = [
            'product_id' => 15, 
            'image' => $makeImagePath(15, 'Laptop_sv_side'), 
            'is_featured' => false
        ];
        
        // 4. Product ID 20 (Thiết bị học tập nâng cao 1 - Status pending, thuộc nhóm ID 20-30)
        $images[] = [
            'product_id' => 20, 
            'image' => $makeImagePath(20, 'Tablet_main'), 
            'is_featured' => true
        ];
        
        // 5. Product ID 31 (Phụ kiện/Tài liệu hỗ trợ 1 - Thuộc nhóm ID 31-40)
        $images[] = [
            'product_id' => 31, 
            'image' => $makeImagePath(31, 'Phu_kien_main'), 
            'is_featured' => true
        ];
        $images[] = [
            'product_id' => 31, 
            'image' => $makeImagePath(31, 'Phu_kien_packaging'), 
            'is_featured' => false
        ];


        // --- USER 2 PRODUCTS (ID 41-50) ---
        
        // 6. Product ID 41 (Tài liệu sinh viên U2 - 1)
        $images[] = [
            'product_id' => 41, 
            'image' => $makeImagePath(41, 'U2_doc_main'), 
            'is_featured' => true
        ];
        
        // 7. Product ID 50 (Tài liệu sinh viên U2 - 10)
        $images[] = [
            'product_id' => 50, 
            'image' => $makeImagePath(50, 'U2_final'), 
            'is_featured' => true
        ];
        
        // 8. Product ID 45 (Thêm một ảnh phụ)
        $images[] = [
            'product_id' => 45, 
            'image' => $makeImagePath(45, 'U2_extra_01'), 
            'is_featured' => true
        ];
        $images[] = [
            'product_id' => 45, 
            'image' => $makeImagePath(45, 'U2_extra_02'), 
            'is_featured' => false
        ];
        


        foreach ($images as $image) {
            ProductImage::create($image);
        }
        
        echo "✅ Đã seed 13 ảnh sản phẩm với cấu trúc storage chuẩn.\n";
    }
}