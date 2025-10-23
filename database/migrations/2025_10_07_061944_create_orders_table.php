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
           
            $table->foreignId('user_id')
                  ->constrained('users') // Tham chiếu đến bảng 'users'
                  ->onDelete('cascade'); // Nếu người dùng bị xóa, order cũng xóa (hoặc set null)
            
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card', 'e_wallet'])->default('cod');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->onDelete('set null');
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
 
            $table->timestamps();
            
            // Đánh chỉ mục
            $table->index('user_id'); 
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