<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Address;
use Illuminate\Support\Str;
class AddressSeeder extends Seeder
{
    public function run()
    {
        $addresses = [
            [
                'user_id' => 2,
                'address_id' => 'ADDR-' . strtoupper(Str::random(8)),
                'full_name' => 'Trần Thị Hương',
                'phone' => '0912345678',
                'province' => 'TP. Hồ Chí Minh',
                'district' => 'Quận 1',
                'ward' => 'Phường Bến Nghé',
                'detail' => '123 Nguyễn Huệ'
            ],
            [
                'user_id' => 2,
                'address_id' => 'ADDR-' . strtoupper(Str::random(8)),
                'full_name' => 'Trần Thị Hương',
                'phone' => '0912345678',
                'province' => 'TP. Hồ Chí Minh',
                'district' => 'Quận 3',
                'ward' => 'Phường Võ Thị Sáu',
                'detail' => '456 Lê Văn Sỹ'
            ],
            [
                'user_id' => 3,
                'address_id' => 'ADDR-' . strtoupper(Str::random(8)),
                'full_name' => 'Lê Văn Nam',
                'phone' => '0923456789',
                'province' => 'Hà Nội',
                'district' => 'Quận Đống Đa',
                'ward' => 'Phường Láng Thượng',
                'detail' => '789 Láng Hạ'
            ],
            [
                'user_id' => 4,
                'address_id' => 'ADDR-' . strtoupper(Str::random(8)),
                'full_name' => 'Phạm Thị Mai',
                'phone' => '0934567890',
                'province' => 'Đà Nẵng',
                'district' => 'Quận Hải Châu',
                'ward' => 'Phường Thạch Thang',
                'detail' => '321 Trần Phú'
            ],
        ];

        foreach ($addresses as $address) {
            Address::create($address);
        }
    }
}