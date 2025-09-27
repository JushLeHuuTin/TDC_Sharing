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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT (primary key)
            $table->string('name'); // tên người dùng
            $table->string('email')->unique(); // email duy nhất
            $table->timestamp('email_verified_at')->nullable(); // thời điểm xác thực email
            $table->string('password'); // mật khẩu đã hash
            $table->rememberToken(); // token để "ghi nhớ đăng nhập"
            $table->timestamps(); // created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
