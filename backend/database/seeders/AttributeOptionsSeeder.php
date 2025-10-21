<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\AttributeOptions;
use Carbon\Carbon;

class AttributeOptionsSeeder extends Seeder
{
    public function run()
    {
        $attributeOptions = [
            // Conversation giữa Admin và Customer 1
            [
                'attribute_id' => 2,
                'value' => "den"
            ],
            [
                'attribute_id' => 1,
                'value' => "trang"
            ],
            [
                'attribute_id' => 2,
                'value' => "xanh"
            ],
            [
                'attribute_id' => 1,
                'value' => "den"
            ],
        ];

        foreach ($attributeOptions as $aittribute_Option) {
            AttributeOptions::create($aittribute_Option);
        }
    }
}