<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SellerSeeder::class,
            BuyerSeeder::class,
            PaymentMethodSeeder::class,
        ]);
        $clientName = 'Personal Access Client';
        $clientExists = DB::table('oauth_clients')->where('name', $clientName)->exists();
        if (!$clientExists) {
            $parameters = [
                '--personal' => true,
                '--name' => $clientName,
            ];
            Artisan::call('passport:client', $parameters);
        }
    }
}
