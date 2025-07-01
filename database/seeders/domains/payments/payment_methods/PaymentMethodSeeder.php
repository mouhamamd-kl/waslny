<?php

namespace Database\Seeders\domains\payments\payment_methods;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Cash',
                'isActive' => true
            ],
            [
                'name' => 'Wallet',
                'isActive' => true
            ],

        ];
        foreach ($paymentMethods as $paymentMethod) {

            PaymentMethod::updateOrCreate(
                [
                    'name' => $paymentMethod['name'],
                    'is_active' => $paymentMethod['isActive'],
                ]
            );
        }
    }
}
