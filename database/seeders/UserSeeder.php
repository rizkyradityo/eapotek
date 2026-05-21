<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@apotek.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Apotek No. 1',
            'is_active' => true,
        ]);

        // Apoteker
        User::create([
            'name' => 'Apoteker',
            'email' => 'apoteker@apotek.com',
            'password' => Hash::make('password'),
            'role' => 'apoteker',
            'phone' => '081234567891',
            'address' => 'Jl. Apotek No. 2',
            'is_active' => true,
        ]);

        // Kasir
        User::create([
            'name' => 'Kasir',
            'email' => 'kasir@apotek.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'phone' => '081234567892',
            'address' => 'Jl. Apotek No. 3',
            'is_active' => true,
        ]);

        // Additional kasir
        User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@apotek.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'phone' => '081234567893',
            'address' => 'Jl. Apotek No. 4',
            'is_active' => true,
        ]);
    }
}
