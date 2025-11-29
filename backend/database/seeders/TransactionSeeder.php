<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        // $transactions = [
        //     [
        //         'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
        //         'order_id' => 1,
        //         'payment_method' => 'bank_transfer',
        //         'transaction_details' => 'Chuyển khoản qua Vietcombank - STK: 1234567890',
        //         'status' => 'completed',
        //         'amount' => 31932000.00
        //     ],
        //     [
        //         'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
        //         'order_id' => 2,
        //         'payment_method' => 'credit_card',
        //         'transaction_details' => 'Thanh toán qua Visa - xxxx-xxxx-xxxx-1234',
        //         'status' => 'completed',
        //         'amount' => 24490000.00
        //     ],
        //     [
        //         'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
        //         'order_id' => 3,
        //         'payment_method' => 'cod',
        //         'transaction_details' => 'Thanh toán khi nhận hàng',
        //         'status' => 'pending',
        //         'amount' => 8990000.00
        //     ],
        //     [
        //         'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
        //         'order_id' => 4,
        //         'payment_method' => 'e_wallet',
        //         'transaction_details' => 'Thanh toán qua MoMo - SĐT: 09xxxxxxxx',
        //         'status' => 'pending',
        //         'amount' => 1340000.00
        //     ],
        //     [
        //         'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
        //         'order_id' => 5,
        //         'payment_method' => 'bank_transfer',
        //         'transaction_details' => 'Đơn hàng bị hủy - Hoàn tiền đã xử lý',
        //         'status' => 'refunded',
        //         'amount' => 45990000.00
        //     ],
        // ];

        // foreach ($transactions as $transaction) {
        //     Transaction::create($transaction);
        // }
    }
}