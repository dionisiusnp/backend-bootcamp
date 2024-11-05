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
        User::create([
            'name' => 'Dion',
            'email' => 'dionisiusnandaa@gmail.com',
            'password' => Hash::make('seller'),
            'is_seller' => true,
            'address' => 'Merr, Surabaya',
            'is_active' => true,
            'inactive_reason' => null,
        ]);
    }
}
