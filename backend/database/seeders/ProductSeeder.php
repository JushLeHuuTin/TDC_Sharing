<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
class ProductSeeder extends Seeder
{
    public function run()
    {
         // 1. Tắt kiểm tra khóa ngoại
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
         // 2. Chạy lệnh TRUNCATE bị lỗi
         // Giả sử đây là dòng bị lỗi: Product::truncate();
         // Product::truncate(); 
         
         // HOẶC dùng DB::table nếu bạn không dùng Eloquent
         DB::table('products')->truncate(); 
         
         // 3. Kích hoạt lại kiểm tra khóa ngoại
         DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $batchSize = 1000; // chèn 1000 bản ghi 1 lần
        $total = 1000000;
        $now = Carbon::now();

        for ($i = 1; $i <= $total; $i += $batchSize) {
            $products = [];

            for ($j = 0; $j < $batchSize && ($i + $j) <= $total; $j++) {
                $num = $i + $j;
                $products[] = [
                    'user_id' => 1,
                    'category_id' => rand(5, 8),
                    'title' => "Sản phẩm số $num",
                    'description' => "Mô tả sản phẩm $num",
                    'price' => rand(20000, 2000000),
                    'status' => ['active','pending','sold','draft'][rand(0,3)],
                    'stocks' => rand(1, 10),
                    'is_visible' => true,
                    'is_featured' => $num % 5 == 0,
                    'views_count' => rand(0, 1000),
                    'slug' => Str::slug("Sản phẩm số $num", '-'),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // chèn batch vào DB
            DB::table('products')->insert($products);

            echo "Inserted batch $i - " . ($i + $batchSize - 1) . "\n";
        }

        echo "✅ Đã seed 1 triệu sản phẩm thành công!\n";
    }
}
