<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        // Tăng cột products_count của danh mục liên quan lên 1
        $product->category()->increment('products_count');
    }

    // Sự kiện khi Sản phẩm bị XÓA
    public function deleted(Product $product): void
    {
        // Giảm cột products_count của danh mục liên quan xuống 1
        $product->category()->decrement('products_count');
    }

    // Sự kiện khi Sản phẩm được CẬP NHẬT (và category_id bị thay đổi)
    public function updated(Product $product): void
    {
        // Nếu category_id CŨ khác category_id MỚI
        if ($product->isDirty('category_id')) {
            // Giảm số lượng của danh mục CŨ
            $product->getOriginal('category_id') 
                    ? (new Product)->category()->associate($product->getOriginal('category_id'))->increment('products_count', -1)
                    : null;

            // Tăng số lượng của danh mục MỚI
            $product->category()->increment('products_count');
        }
    }
}
