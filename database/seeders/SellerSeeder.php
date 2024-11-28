<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Dion',
                'email' => 'dionisiusnandaa@gmail.com',
                'password' => Hash::make('seller'),
                'is_seller' => true,
                'address' => 'Merr, Surabaya',
                'is_active' => true,
                'inactive_reason' => null,
            ]
        );
    }
}