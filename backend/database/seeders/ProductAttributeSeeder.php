<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Attribute;

class ProductAttributeSeeder extends Seeder
{
    public function run()
    {
        $batchSize = 500;
        $dataBatch = [];

        // ❗ Chỉ lấy 1000 sản phẩm
        $products = Product::select('id', 'category_id')
                            ->limit(1000)
                            ->get();

        foreach ($products as $product) {

            // Lấy attribute theo category của product
            $attributes = Attribute::where('category_id', $product->category_id)->get();

            foreach ($attributes as $attr) {
                $dataBatch[] = [
                    'product_id'   => $product->id,
                    'attribute_id' => $attr->id,
                    'value'        => $this->fakeValue($attr->data_type),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                // Insert theo batch
                if (count($dataBatch) >= $batchSize) {
                    DB::table('product_attributes')->insert($dataBatch);
                    $dataBatch = [];
                }
            }
        }

        // Insert phần còn lại
        if (!empty($dataBatch)) {
            DB::table('product_attributes')->insert($dataBatch);
        }

        echo "✔ Seed attribute thành công cho 1000 sản phẩm!\n";
    }

    private function fakeValue($type)
    {
        return match ($type) {
            'NUMBER' => rand(1, 100),
            'TEXT'   => fake()->sentence(2),
            'select' => fake()->randomElement(['A', 'B', 'C', 'D']),
            default  => fake()->word(),
        };
    }
}
