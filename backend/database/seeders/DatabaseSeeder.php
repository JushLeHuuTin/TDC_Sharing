<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Gọi các seeders theo đúng thứ tự (quan trọng vì có foreign keys)
        $this->call([
            UserSeeder::class,              // 1. Users trước (không phụ thuộc)
            AddressSeeder::class,           // 2. Addresses (phụ thuộc Users)
            CategorySeeder::class,          // 3. Categories (không phụ thuộc)
            AttributeSeeder::class,         // 4. Attributes (phụ thuộc Categories)
            ProductSeeder::class,           // 5. Products (phụ thuộc Users, Categories)
            ProductImageSeeder::class,      // 6. Product Images (phụ thuộc Products)
            ProductAttributeSeeder::class,  // 7. Product Attributes (phụ thuộc Products, Attributes)
            VoucherSeeder::class,           // 8. Vouchers (không phụ thuộc)
            CartSeeder::class,              // 9. Carts (phụ thuộc Users)
            CartItemSeeder::class,          // 10. Cart Items (phụ thuộc Carts, Products)
            OrderSeeder::class,             // 11. Orders (phụ thuộc Users, Addresses, Vouchers)
            OrderItemSeeder::class,         // 12. Order Items (phụ thuộc Orders, Products)
            WishlistSeeder::class,          // 13. Wishlists (phụ thuộc Users, Products)
            ReviewSeeder::class,            // 14. Reviews (phụ thuộc Users, Products)
            PromotionSeeder::class,         // 15. Promotions (không phụ thuộc)
            TransactionSeeder::class,       // 16. Transactions (phụ thuộc Orders)
            AttributeOptionsSeeder::class,           
            NotificationSeeder::class,      // 18. Notifications (phụ thuộc Users)
        ]);
        
        $this->command->info('✅ Đã seed xong tất cả dữ liệu!');
        $this->command->info('📊 Thống kê:');
        $this->command->info('   - Users: ' . \App\Models\User::count());
        $this->command->info('   - Products: ' . \App\Models\Product::count());
        $this->command->info('   - Orders: ' . \App\Models\Order::count());
        $this->command->info('   - Reviews: ' . \App\Models\Review::count());
        $this->command->info('   - Categories: ' . \App\Models\Category::count());
    }
}