<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::truncate();

        $products = [];

        // Helper tạo slug
        $makeSlug = fn($title) => Str::slug($title, '-');

        // --- USER 1 ---
        // 10 sp active category 5
        for ($i = 1; $i <= 50; $i++) {
            $title = "Giáo trình chuyên ngành số $i";
            $products[] = [
                'user_id' => 1,
                'category_id' => 5,
                'title' => $title,
                'description' => "Sách giáo trình dùng trong học kỳ $i, bản in rõ đẹp.",
                'price' => rand(50000, 200000),
                'status' => 'active',
                'stocks' => rand(1, 3),
                'is_visible' => true,
                'is_featured' => $i % 3 == 0,
                'views_count' => rand(20, 200),
                'slug' => $makeSlug($title)
            ];
        }

        // 9 sp active category 7
        for ($i = 1; $i <= 9; $i++) {
            $title = "Laptop sinh viên $i";
            $products[] = [
                'user_id' => 1,
                'category_id' => 7,
                'title' => $title,
                'description' => "Laptop học tập, hiệu năng ổn định cho sinh viên lập trình.",
                'price' => rand(7000000, 15000000),
                'status' => 'active',
                'stocks' => 1,
                'is_visible' => true,
                'is_featured' => $i % 2 == 0,
                'views_count' => rand(50, 300),
                'slug' => $makeSlug($title)
            ];
        }

        // 11 sp category 7 chia status pending, sold, draft, hidden
        $statuses = ['pending', 'sold', 'draft', 'hidden'];
        for ($i = 1; $i <= 11; $i++) {
            $status = $statuses[$i % count($statuses)];
            $title = "Thiết bị học tập nâng cao $i";
            $products[] = [
                'user_id' => 1,
                'category_id' => 7,
                'title' => $title,
                'description' => "Sản phẩm công nghệ hỗ trợ việc học, tình trạng: $status.",
                'price' => rand(300000, 5000000),
                'status' => $status,
                'stocks' => rand(1, 5),
                'is_visible' => true,
                'is_featured' => $i % 4 == 0,
                'views_count' => rand(10, 400),
                'slug' => $makeSlug($title)
            ];
        }

        // 10 sp còn lại chia ngẫu nhiên
        $extraCategories = [6, 8, 9, 10];
        $extraStatuses = ['active', 'pending', 'sold', 'draft'];
        for ($i = 1; $i <= 10; $i++) {
            $cat = $extraCategories[array_rand($extraCategories)];
            $status = $extraStatuses[array_rand($extraStatuses)];
            $title = "Phụ kiện / Tài liệu hỗ trợ $i";
            $products[] = [
                'user_id' => 1,
                'category_id' => $cat,
                'title' => $title,
                'description' => "Sản phẩm hỗ trợ học tập thuộc danh mục $cat.",
                'price' => rand(20000, 400000),
                'status' => $status,
                'stocks' => rand(1, 10),
                'is_visible' => true,
                'is_featured' => $i % 5 == 0,
                'views_count' => rand(5, 100),
                'slug' => $makeSlug($title)
            ];
        }

        // --- USER 2 ---
        $user2Categories = [5, 6, 7, 8];
        for ($i = 1; $i <= 10; $i++) {
            $cat = $user2Categories[array_rand($user2Categories)];
            $title = "Tài liệu sinh viên U2 - $i";
            $products[] = [
                'user_id' => 2,
                'category_id' => $cat,
                'title' => $title,
                'description' => "Sản phẩm do người dùng 2 đăng, danh mục $cat.",
                'price' => rand(40000, 250000),
                'status' => 'active',
                'stocks' => rand(1, 5),
                'is_visible' => true,
                'is_featured' => $i % 2 == 0,
                'views_count' => rand(15, 120),
                'slug' => $makeSlug($title)
            ];
        }

        foreach ($products as $p) {
            Product::create($p);
        }

        echo "✅ Đã seed 50 sản phẩm (User1:40, User2:10) thành công!\n";
    }
}
