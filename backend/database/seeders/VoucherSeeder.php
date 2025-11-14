<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VoucherSeeder extends Seeder
{
    public function run()
    {
        $vouchers = [
            [
                'code' => 'NEWUSER2024',
                'description' => 'Giảm 10% cho khách hàng mới',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'min_purchase' => 500000.00,
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addMonths(3),
                'usage_limit' => 100,
                'used_count' => 15
            ],
            [
                'code' => 'SALE500K',
                'description' => 'Giảm 500.000đ cho đơn hàng từ 5 triệu',
                'discount_type' => 'fixed',
                'discount_value' => 500000.00,
                'min_purchase' => 5000000.00,
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addMonths(2),
                'usage_limit' => 50,
                'used_count' => 8
            ],
            [
                'code' => 'FREESHIP',
                'description' => 'Miễn phí vận chuyển cho đơn từ 1 triệu',
                'discount_type' => 'fixed',
                'discount_value' => 50000.00,
                'min_purchase' => 1000000.00,
                'start_date' => Carbon::now()->subDays(20),
                'end_date' => Carbon::now()->addMonths(1),
                'usage_limit' => 200,
                'used_count' => 45
            ],
            [
                'code' => 'TECH2024',
                'description' => 'Giảm 15% cho sản phẩm công nghệ',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'min_purchase' => 2000000.00,
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addMonths(4),
                'usage_limit' => 150,
                'used_count' => 32
            ],
            [
                'code' => 'EXPIRED2023',
                'description' => 'Voucher đã hết hạn',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'min_purchase' => 1000000.00,
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->subDays(1),
                'usage_limit' => 100,
                'used_count' => 100
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }
    }
}
