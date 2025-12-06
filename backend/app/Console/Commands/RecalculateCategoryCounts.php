<?php

namespace App\Console\Commands;
// app/Console/Commands/RecalculateCategoryCounts.php
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateCategoryCounts extends Command
{
    protected $signature = 'counts:recalculate';
    protected $description = 'Recalculates product counts for all categories.';

    public function handle()
    {
        $this->info('Starting recalculation...');

        // Dùng Query Builder để tính toán hiệu quả hơn
        $counts = DB::table('products')
                    ->select('category_id', DB::raw('count(*) as total'))
                    ->groupBy('category_id')
                    ->pluck('total', 'category_id');

        foreach ($counts as $categoryId => $count) {
            Category::where('id', $categoryId)->update(['products_count' => $count]);
        }

        $this->info('Recalculation finished!');
    }
}