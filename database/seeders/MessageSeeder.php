<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Message;
use Carbon\Carbon;

class MessageSeeder extends Seeder
{
    public function run()
    {
        $messages = [
            // Conversation giữa Admin và Customer 1
            [
                'sender_id' => 2,
                'receiver_id' => 1,
                'content' => 'Xin chào Admin, tôi muốn hỏi về sản phẩm iPhone 15 Pro Max có còn hàng không ạ?',
                'sent_at' => Carbon::now()->subDays(3)->subHours(2),
                'is_read' => true
            ],
            [
                'sender_id' => 1,
                'receiver_id' => 2,
                'content' => 'Chào bạn! iPhone 15 Pro Max hiện vẫn còn hàng. Bạn muốn màu nào ạ?',
                'sent_at' => Carbon::now()->subDays(3)->subHours(1)->subMinutes(30),
                'is_read' => true
            ],
            [
                'sender_id' => 2,
                'receiver_id' => 1,
                'content' => 'Tôi muốn màu Titan Tự nhiên. Có thể giao hàng trong ngày không?',
                'sent_at' => Carbon::now()->subDays(3)->subHours(1),
                'is_read' => true
            ],
            [
                'sender_id' => 1,
                'receiver_id' => 2,
                'content' => 'Dạ có ạ! Nếu đặt hàng trước 12h trưa thì chúng tôi sẽ giao trong ngày. Bạn đặt hàng luôn nhé!',
                'sent_at' => Carbon::now()->subDays(3)->subMinutes(45),
                'is_read' => true
            ],
            
            // Conversation giữa Admin và Customer 2
            [
                'sender_id' => 3,
                'receiver_id' => 1,
                'content' => 'Shop ơi, tôi đã đặt đơn hàng từ 2 ngày trước nhưng chưa thấy giao?',
                'sent_at' => Carbon::now()->subDays(1)->subHours(5),
                'is_read' => true
            ],
            [
                'sender_id' => 1,
                'receiver_id' => 3,
                'content' => 'Dạ để shop kiểm tra đơn hàng của bạn. Mã đơn hàng là gì ạ?',
                'sent_at' => Carbon::now()->subDays(1)->subHours(4)->subMinutes(30),
                'is_read' => true
            ],
            [
                'sender_id' => 3,
                'receiver_id' => 1,
                'content' => 'Mã ORD-ABC123 ạ',
                'sent_at' => Carbon::now()->subDays(1)->subHours(4),
                'is_read' => true
            ],
            [
                'sender_id' => 1,
                'receiver_id' => 3,
                'content' => 'Đơn hàng của bạn đang trên đường giao, dự kiến giao trong hôm nay. Xin lỗi vì sự chậm trễ!',
                'sent_at' => Carbon::now()->subDays(1)->subHours(3)->subMinutes(45),
                'is_read' => true
            ],
            
            // Conversation giữa Customer 1 và Customer 2
            [
                'sender_id' => 2,
                'receiver_id' => 3,
                'content' => 'Bạn đã dùng MacBook Pro chưa? Có nên mua không?',
                'sent_at' => Carbon::now()->subHours(12),
                'is_read' => true
            ],
            [
                'sender_id' => 3,
                'receiver_id' => 2,
                'content' => 'Mình đã mua rồi, máy rất tốt! Chip M3 Pro mạnh lắm, render video siêu nhanh.',
                'sent_at' => Carbon::now()->subHours(11)->subMinutes(30),
                'is_read' => true
            ],
            [
                'sender_id' => 2,
                'receiver_id' => 3,
                'content' => 'Vậy à, mình định mua luôn. Cảm ơn bạn nhé!',
                'sent_at' => Carbon::now()->subHours(11),
                'is_read' => false
            ],
        ];

        foreach ($messages as $message) {
            Message::create($message);
        }
    }
}