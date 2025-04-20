<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Bus;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BusinessOwnerSeeder::class,
            AdminSeeder::class,
            InventoryManagerSeeder::class,
            CashierSeeder::class,
            // tambah seeder lain di sini jika ada
        ]);
    }
}
