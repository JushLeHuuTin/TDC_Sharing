<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
class ReviewSeeder extends Seeder
{
    public function run()
    {
        $reviews = [
            [
                'product_id' => 1, // iPhone 15 Pro Max
                'reviewer_id' => 2,
                'rating' => 5,
                'comment' => 'Sản phẩm tuyệt vời! Màn hình đẹp, camera chụp ảnh rất sắc nét. Rất đáng tiền!'
            ],
            [
                'product_id' => 1,
                'reviewer_id' => 3,
                'rating' => 4,
                'comment' => 'Máy đẹp, pin trâu. Nhưng giá hơi cao so với các dòng khác.'
            ],
            [
                'product_id' => 2, // iPhone 14 Pro
                'reviewer_id' => 4,
                'rating' => 5,
                'comment' => 'Máy chạy mượt, Dynamic Island rất hay. Shop giao hàng nhanh!'
            ],
            [
                'product_id' => 3, // Samsung S24 Ultra
                'reviewer_id' => 2,
                'rating' => 5,
                'comment' => 'Camera 200MP quá đỉnh! Bút S Pen viết mượt. Flagship tốt nhất 2024.'
            ],
            [
                'product_id' => 5, // MacBook Pro
                'reviewer_id' => 3,
                'rating' => 5,
                'comment' => 'Chip M3 Pro mạnh mẽ, render video siêu nhanh. Đáng đồng tiền bát gạo!'
            ],
            [
                'product_id' => 9, // AirPods Pro
                'reviewer_id' => 2,
                'rating' => 4,
                'comment' => 'Chống ồn tốt, âm thanh trong. Hơi đắt nhưng chất lượng xứng đáng.'
            ],
            [
                'product_id' => 9,
                'reviewer_id' => 4,
                'rating' => 5,
                'comment' => 'Âm thanh hay, pin trâu. Kết nối nhanh với iPhone. Rất hài lòng!'
            ],
            [
                'product_id' => 10, // Sony WH
                'reviewer_id' => 3,
                'rating' => 5,
                'comment' => 'Chống ồn cực tốt! Âm thanh Hi-Res tuyệt vời. Đeo cả ngày không mỏi tai.'
            ],
            [
                'product_id' => 7, // iPad Pro
                'reviewer_id' => 4,
                'rating' => 4,
                'comment' => 'Màn hình đẹp, chip M2 mạnh. Nhưng giá hơi cao. Dùng để vẽ rất tốt.'
            ],
            [
                'product_id' => 11, // Anker PowerCore
                'reviewer_id' => 2,
                'rating' => 5,
                'comment' => 'Pin sạc nhanh, dung lượng lớn. Mang đi du lịch rất tiện. Giá tốt!'
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}