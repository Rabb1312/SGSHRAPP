<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Users;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Users::create([
            'email' => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'name' => 'Administrator',
            'role' => 'admin',
            'status' => true,
            'created_by' => 1
        ]);

        Users::create([
            'email' => 'hr@test.com',
            'password' => Hash::make('admin123'),
            'name' => 'HR Manager',
            'role' => 'hr_manager',
            'status' => true,
            'created_by' => 1
        ]);

        // Optional: Tambah user test lainnya
        Users::create([
            'email' => 'user@test.com',
            'password' => Hash::make('admin123'),
            'name' => 'Regular User',
            'role' => 'user',
            'status' => true,
            'created_by' => 1
        ]);
    }
}
