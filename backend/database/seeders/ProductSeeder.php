<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ để tránh trùng lặp
        Product::truncate();
        
        $products = [
            // --- TÀI NGUYÊN HỌC TẬP (Category ID 5 & 6) ---
            [
                'user_id' => 1,
                'category_id' => 5, // Giáo trình & Sách chuyên ngành
                'title' => 'Giáo trình Lập trình Hướng đối tượng C++',
                'description' => 'Sách giáo trình bản gốc, còn mới 95%, có highlight một vài chỗ quan trọng. Kèm theo bài tập lab đã giải.',
                'price' => 120000.00,
                'status' => 'active',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 50,
                'slug' => 'giao-trinh-lap-trinh-c++'
            ],
            [
                'user_id' => 2,
                'category_id' => 6, // Đề thi & Bài tập
                'title' => 'Tài liệu ôn thi TOEIC 750+ (Có file nghe)',
                'description' => 'Bộ 5 đề thi thử TOEIC mới nhất, kèm transcript và audio chất lượng cao. Đã in sẵn.',
                'price' => 50000.00,
                'status' => 'active',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 80,
                'slug' => 'tai-lieu-on-thi-toeic-750'
            ],
            [
                'user_id' => 1,
                'category_id' => 5, 
                'title' => 'Sách "Kinh tế vi mô" - Tái bản mới nhất',
                'description' => 'Sách còn nguyên seal, chưa bóc. Mua về nhưng không dùng đến. Giá bìa 200k.',
                'price' => 150000.00,
                'status' => 'new',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 35,
                'slug' => 'sach-kinh-te-vi-mo-moi'
            ],
            [
                'user_id' => 1,
                'category_id' => 6, 
                'title' => 'Bộ đề cương ôn thi giữa kỳ môn Giải tích 1 (Dạng PDF)',
                'description' => 'Tài liệu tổng hợp các dạng bài tập quan trọng và các đề thi giữa kỳ năm trước. Chỉ bán bản mềm.',
                'price' => 30000.00,
                'status' => 'draft',
                'stocks' => 999, // Tài liệu mềm có thể bán nhiều
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 120,
                'slug' => 'bo-de-cuong-giai-tich-1'
            ],

            // --- THIẾT BỊ & CÔNG NGHỆ (Category ID 7 & 8) ---
            [
                'user_id' => 1,
                'category_id' => 7, // Laptop, PC & Linh kiện
                'title' => 'Laptop Dell Inspiron 14 5402 (Đã qua sử dụng)',
                'description' => 'Laptop Core i5 Gen 11, RAM 8GB, SSD 512GB. Mua được 1 năm, ngoại hình 90%, thích hợp làm bài tập và code cơ bản.',
                'price' => 9500000.00,
                'status' => 'pending',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 150,
                'slug' => 'laptop-dell-inspiron-14-used'
            ],
            [
                'user_id' => 1,
                'category_id' => 8, // Phụ kiện học tập
                'title' => 'Tai nghe Sony WH-CH510 (Màu đen, còn hộp)',
                'description' => 'Tai nghe Bluetooth không dây, pin trâu, âm thanh tốt để nghe giảng và học ngoại ngữ. Còn bảo hành 3 tháng.',
                'price' => 350000.00,
                'status' => 'sold',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => true,
                'views_count' => 60,
                'slug' => 'tai-nghe-sony-wh-ch510'
            ],
            [
                'user_id' => 1,
                'category_id' => 7, 
                'title' => 'Màn hình máy tính ASUS 24 inch (Cũ)',
                'description' => 'Màn hình Full HD, 75Hz, không điểm chết. Rất phù hợp cho sinh viên thiết kế hoặc lập trình.',
                'price' => 1800000.00,
                'status' => 'hidden',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 45,
                'slug' => 'man-hinh-asus-24-inch'
            ],
            [
                'user_id' => 1,
                'category_id' => 8, 
                'title' => 'Pin sạc dự phòng Xiaomi 10000mAh (99%)',
                'description' => 'Sạc dự phòng nhỏ gọn, mới dùng vài lần, sạc nhanh 18W. Kèm cáp sạc.',
                'price' => 250000.00,
                'status' => 'active',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => false,
                'views_count' => 30,
                'slug' => 'sac-du-phong-xiaomi-10000mah'
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}