<?php

namespace Database\Seeders;

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
                'id' => 1,
                'type' => 'COD',
                'description' => 'Cash on Delivery',
                'is_available' => true,
            ],
            [
                'id' => 2,
                'type' => 'Bank Transfer',
                'description' => 'Transfer melalui rekening bank',
                'is_available' => true,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['id' => $method['id']],
                $method
            );
        }
    }
}