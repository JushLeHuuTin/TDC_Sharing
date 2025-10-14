<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    public function run()
    {
        $promotions = [
            [
                'name' => 'Flash Sale Cuối Tuần',
                'description' => 'Giảm giá sốc cuối tuần cho tất cả sản phẩm điện thoại',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_purchase' => 10000000.00,
                'max_discount' => 5000000.00,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(5),
                'usage_limit' => 100,
                'per_customer_limit' => 1,
                'is_active' => true,
                'applicable_products' => json_encode([1, 2, 3, 4]),
                'applicable_categories' => json_encode([1, 5, 6])
            ],
            [
                'name' => 'Khuyến Mãi Laptop Gaming',
                'description' => 'Giảm 3 triệu cho laptop gaming cao cấp',
                'discount_type' => 'fixed',
                'discount_value' => 3000000.00,
                'min_purchase' => 30000000.00,
                'max_discount' => null,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addMonths(1),
                'usage_limit' => 50,
                'per_customer_limit' => 2,
                'is_active' => true,
                'applicable_products' => json_encode([5, 6]),
                'applicable_categories' => json_encode([2])
            ],
            [
                'name' => 'Mua 1 Tặng 1 Phụ Kiện',
                'description' => 'Mua phụ kiện tặng ngay phụ kiện khác',
                'discount_type' => 'percentage',
                'discount_value' => 50.00,
                'min_purchase' => 500000.00,
                'max_discount' => 1000000.00,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'usage_limit' => 200,
                'per_customer_limit' => 5,
                'is_active' => true,
                'applicable_products' => json_encode([9, 10, 11, 12]),
                'applicable_categories' => json_encode([4, 7, 8])
            ],
            [
                'name' => 'Sale Tết 2024',
                'description' => 'Chương trình khuyến mãi Tết Nguyên Đán',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'min_purchase' => 2000000.00,
                'max_discount' => 3000000.00,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->subMonths(1),
                'usage_limit' => 500,
                'per_customer_limit' => 3,
                'is_active' => false,
                'applicable_products' => json_encode([]),
                'applicable_categories' => json_encode([])
            ],
        ];

        foreach ($promotions as $promotion) {
            Promotion::create($promotion);
        }
    }
}