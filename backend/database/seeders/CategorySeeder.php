<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str; // Đảm bảo lớp Str được sử dụng nếu cần

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // --- DANH MỤC CẤP 1 ---
            [
                'parent_id' => null,
                'name' => 'Tài liệu học tập', // ⬅️ Đã đổi tên
                'description' => 'Tài liệu, giáo trình, bài giảng, đề thi các môn học',
                'icon' => 'fas fa-book-open', // ⬅️ Icon phù hợp hơn
                'color' => '#188E69', // Xanh lá cây đậm
                'display_order' => 1,
                'is_visible' => true,
                'slug' => 'tai-lieu-hoc-tap'
            ],
            [
                'parent_id' => null,
                'name' => 'Thiết bị & Công nghệ', // ⬅️ Đã đổi tên
                'description' => 'Thiết bị điện tử phục vụ học tập, đồ công nghệ cũ/mới',
                'icon' => 'fas fa-laptop-code', // ⬅️ Icon mới
                'color' => '#0d6efd', // Xanh dương (Blue Primary)
                'display_order' => 2,
                'is_visible' => true,
                'slug' => 'thiet-bi-cong-nghe'
            ],
            [
                'parent_id' => null,
                'name' => 'Đồ dùng cá nhân', // ⬅️ Đã đổi tên
                'description' => 'Quần áo, đồ dùng sinh hoạt cá nhân, mỹ phẩm, đồ handmade',
                'icon' => 'fas fa-tshirt',
                'color' => '#e83e8c', // Hồng (Pink)
                'display_order' => 3,
                'is_visible' => true,
                'slug' => 'do-dung-ca-nhan'
            ],
            [
                'parent_id' => null,
                'name' => 'Kỹ năng & Phát triển', // ⬅️ Danh mục mới
                'description' => 'Chia sẻ khóa học, kinh nghiệm phỏng vấn, tài liệu phát triển bản thân',
                'icon' => 'fas fa-brain',
                'color' => '#764ba2', // Tím (Purple)
                'display_order' => 4,
                'is_visible' => true,
                'slug' => 'ky-nang-phat-trien'
            ],
            
            // --- DANH MỤC CẤP 2 (Con của Tài liệu học tập: ID 1) ---
            [
                'parent_id' => 1,
                'name' => 'Giáo trình & Sách chuyên ngành',
                'description' => 'Giáo trình, sách tham khảo, đề cương theo chuyên ngành',
                'icon' => 'fas fa-book',
                'color' => '#FF6B6B',
                'display_order' => 1,
                'is_visible' => true,
                'slug' => 'giao-trinh-sach-chuyen-nganh'
            ],
            [
                'parent_id' => 1,
                'name' => 'Đề thi & Bài tập',
                'description' => 'Đề thi cuối kỳ, bài tập lớn, bài tập mẫu có lời giải',
                'icon' => 'fas fa-file-alt',
                'color' => '#FF8C42',
                'display_order' => 2,
                'is_visible' => true,
                'slug' => 'de-thi-bai-tap'
            ],
            
            // --- DANH MỤC CẤP 2 (Con của Thiết bị & Công nghệ: ID 2) ---
            [
                'parent_id' => 2,
                'name' => 'Laptop, PC & Linh kiện',
                'description' => 'Laptop, PC, các linh kiện nâng cấp máy tính',
                'icon' => 'fas fa-desktop', // ⬅️ Icon mới
                'color' => '#4ECDC4',
                'display_order' => 1,
                'is_visible' => true,
                'slug' => 'laptop-pc-linh-kien'
            ],
            [
                'parent_id' => 2,
                'name' => 'Phụ kiện học tập',
                'description' => 'Bút cảm ứng, tai nghe, pin sạc, thiết bị ngoại vi',
                'icon' => 'fas fa-headset',
                'color' => '#6BCB77',
                'display_order' => 2,
                'is_visible' => true,
                'slug' => 'phu-kien-hoc-tap'
            ],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['name' => $data['name']], // điều kiện duy nhất
                $data // dữ liệu để cập nhật hoặc tạo mới
            );
        }
    }
}