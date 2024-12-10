<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BuyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Dionisius NP',
                'email' => 'dion@gmail.com',
                'password' => Hash::make('buyer'),
                'is_seller' => false,
                'address' => 'Dinamika, Surabaya',
                'is_active' => true,
                'inactive_reason' => null,
            ]
        );
    }
}
