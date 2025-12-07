<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'email' => 'admin@ecommerce.com',
                'password' => Hash::make('admin123'),
                'phone' => '0901234567',
                'full_name' => 'Nguyễn Văn Admin',
                'status' => 'active',
                'role' => 'admin',
                'username' => 'admin'
            ],
            [
                'email' => 'customer1@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => '0912345678',
                'full_name' => 'Trần Thị Hương',
                'status' => 'active',
                'role' => 'customer',
                'username' => 'huongtran'
            ],
            [
                'email' => 'customer2@gmail.com',
                'password' => Hash::make('customer123'),
                'phone' => '0923456789',
                'full_name' => 'Lê Văn Nam',
                'status' => 'active',
                'role' => 'customer',
                'username' => 'namle'
            ],
            [
                'email' => 'customer3@gmail.com',
                'password' => Hash::make('customer123'),
                'phone' => '0934567890',
                'full_name' => 'Phạm Thị Mai',
                'status' => 'active',
                'role' => 'customer',
                'username' => 'maipham'
            ],
            [
                'email' => 'customer4@gmail.com',
                'password' => Hash::make('customer123'),
                'phone' => '0945678901',
                'full_name' => 'Hoàng Văn Đức',
                'status' => 'inactive',
                'role' => 'customer',
                'username' => 'duchoang'
            ],
        ];

        foreach ($users as $data) {
            // 🔹 Kiểm tra theo email, nếu tồn tại thì cập nhật lại thông tin
            User::updateOrCreate(
                ['email' => $data['email']], // điều kiện duy nhất
                $data // dữ liệu để cập nhật hoặc tạo mới
            );
        }

        $this->command->info('✅ UserSeeder đã chạy xong mà không tạo bản ghi trùng lặp.');
    }
}
