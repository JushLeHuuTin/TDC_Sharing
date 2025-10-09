<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
// Gọi seeders theo thứ tự
$this->call([
    UserSeeder::class,
    AddressSeeder::class,
    CategorySeeder::class,
    AttributeSeeder::class,
    ProductSeeder::class,
    ProductImageSeeder::class,
    ProductAttributeSeeder::class,
    VoucherSeeder::class,
    CartSeeder::class,
    CartItemSeeder::class,
    OrderSeeder::class,
    OrderItemSeeder::class,
    WishlistSeeder::class,
    ReviewSeeder::class,
    PromotionSeeder::class,
    TransactionSeeder::class,
    MessageSeeder::class,
    NotificationSeeder::class,
]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
