<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@amtracker.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // Create sample Area Managers
        User::create([
            'name' => 'John Doe',
            'email' => 'john@amtracker.com',
            'password' => Hash::make('password'),
            'role' => 'am',
            'phone' => '081234567891',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@amtracker.com',
            'password' => Hash::make('password'),
            'role' => 'am',
            'phone' => '081234567892',
            'address' => 'Jl. Thamrin No. 5, Jakarta',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@amtracker.com',
            'password' => Hash::make('password'),
            'role' => 'am',
            'phone' => '081234567893',
            'address' => 'Jl. Gatot Subroto No. 10, Jakarta',
            'is_active' => true,
        ]);
    }
}
