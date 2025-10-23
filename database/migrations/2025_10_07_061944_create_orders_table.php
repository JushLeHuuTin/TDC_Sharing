<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // 2. ID Người MUA (Người tạo đơn hàng hiện tại - THAY CHO user_id cũ)
            // LƯU Ý: Nếu không muốn buyer_user_id NULL, hãy xóa ->nullable()
            $table->foreignId('buyer_user_id')
                  ->constrained('users') // Tham chiếu đến bảng 'users'
                  ->onDelete('cascade'); // Nếu người dùng bị xóa, order cũng xóa (hoặc set null)

            // 3. (TÙY CHỌN): Seller ID (ID của người bán đầu tiên/chính)
            // Cột này được giữ lại nếu Order Model đại diện cho 1 Seller cụ thể,
            // nhưng thường được xử lý trong bảng order_items (theo ERD của bạn)
            // Nếu bạn không dùng nó, hãy xóa nó. Tôi tạm giữ với tên rõ ràng.
            // $table->foreignId('seller_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // 4. Các trường khác
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card', 'e_wallet'])->default('cod');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
 
            $table->timestamps();
            
            // Đánh chỉ mục
            $table->index('buyer_user_id'); // Đổi từ user_id sang buyer_user_id
            $table->index('order_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};