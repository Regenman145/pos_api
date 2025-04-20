<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InventoryManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Inventory Manager',
            'email' => 'inventory@example.com',
            'password' => Hash::make('inventory123'),
            'role' => 'inventory',
        ]);
    }
}
