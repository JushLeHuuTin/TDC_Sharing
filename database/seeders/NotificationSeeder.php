<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $notifications = [
            // Notifications cho User 2
            [
                'user_id' => 2,
                'content' => 'Đơn hàng #ORD-ABC123 của bạn đã được giao thành công',
                'type' => 'order',
                'is_read' => true,
                'object' => json_encode(['order_id' => 1, 'status' => 'delivered'])
            ],
            [
                'user_id' => 2,
                'content' => 'Flash Sale Cuối Tuần - Giảm giá đến 20% cho điện thoại',
                'type' => 'promotion',
                'is_read' => true,
                'object' => json_encode(['promotion_id' => 1])
            ],
            [
                'user_id' => 2,
                'content' => 'Bạn có tin nhắn mới từ Admin',
                'type' => 'message',
                'is_read' => false,
                'object' => json_encode(['message_id' => 4, 'sender_id' => 1])
            ],
            [
                'user_id' => 2,
                'content' => 'Sản phẩm iPhone 15 Pro Max trong wishlist của bạn đang giảm giá!',
                'type' => 'promotion',
                'is_read' => false,
                'object' => json_encode(['product_id' => 1, 'discount' => 15])
            ],
            
            // Notifications cho User 3
            [
                'user_id' => 3,
                'content' => 'Đơn hàng #ORD-DEF456 đang được giao đến bạn',
                'type' => 'order',
                'is_read' => true,
                'object' => json_encode(['order_id' => 2, 'status' => 'shipped'])
            ],
            [
                'user_id' => 3,
                'content' => 'Đơn hàng #ORD-GHI789 đã bị hủy',
                'type' => 'order',
                'is_read' => true,
                'object' => json_encode(['order_id' => 5, 'status' => 'cancelled'])
            ],
            [
                'user_id' => 3,
                'content' => 'Khuyến Mãi Laptop Gaming - Giảm 3 triệu cho laptop cao cấp',
                'type' => 'promotion',
                'is_read' => false,
                'object' => json_encode(['promotion_id' => 2])
            ],
            [
                'user_id' => 3,
                'content' => 'Bạn có tin nhắn mới từ Trần Thị Hương',
                'type' => 'message',
                'is_read' => false,
                'object' => json_encode(['message_id' => 11, 'sender_id' => 2])
            ],
            
            // Notifications cho User 4
            [
                'user_id' => 4,
                'content' => 'Đơn hàng #ORD-JKL012 đang chờ xử lý',
                'type' => 'order',
                'is_read' => true,
                'object' => json_encode(['order_id' => 4, 'status' => 'pending'])
            ],
            [
                'user_id' => 4,
                'content' => 'Chào mừng bạn đến với E-commerce! Nhận ngay mã giảm giá 10%',
                'type' => 'system',
                'is_read' => true,
                'object' => json_encode(['voucher_code' => 'NEWUSER2024'])
            ],
            [
                'user_id' => 4,
                'content' => 'Mua 1 Tặng 1 Phụ Kiện - Khuyến mãi hot nhất tháng',
                'type' => 'promotion',
                'is_read' => false,
                'object' => json_encode(['promotion_id' => 3])
            ],
            [
                'user_id' => 4,
                'content' => 'Sản phẩm MacBook Pro trong wishlist của bạn đã có hàng trở lại',
                'type' => 'system',
                'is_read' => false,
                'object' => json_encode(['product_id' => 5, 'stock' => 25])
            ],
            
            // Notifications cho Admin (User 1)
            [
                'user_id' => 1,
                'content' => 'Có đơn hàng mới cần xử lý #ORD-ABC123',
                'type' => 'order',
                'is_read' => true,
                'object' => json_encode(['order_id' => 1, 'customer_id' => 2])
            ],
            [
                'user_id' => 1,
                'content' => 'Bạn có tin nhắn mới từ khách hàng Trần Thị Hương',
                'type' => 'message',
                'is_read' => true,
                'object' => json_encode(['message_id' => 1, 'sender_id' => 2])
            ],
            [
                'user_id' => 1,
                'content' => 'Sản phẩm iPhone 15 Pro Max sắp hết hàng (còn 50 sản phẩm)',
                'type' => 'system',
                'is_read' => false,
                'object' => json_encode(['product_id' => 1, 'stock' => 50])
            ],
            [
                'user_id' => 1,
                'content' => 'Có đánh giá mới cho sản phẩm Samsung Galaxy S24 Ultra',
                'type' => 'system',
                'is_read' => false,
                'object' => json_encode(['review_id' => 4, 'product_id' => 3, 'rating' => 5])
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }
    }
}