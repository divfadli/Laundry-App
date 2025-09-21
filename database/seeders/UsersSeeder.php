<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id_level' => 1, // Administrator
            'name' => 'Super Admin',
            'email' => 'admin@laundry.com',
            'password' => Hash::make('password')
        ]);
        User::create([
            'id_level' => 2, // Operator
            'name' => 'Operator Laundry',
            'email' => 'operator@laundry.com',
            'password' => Hash::make('password')
        ]);
        User::create([
            'id_level' => 3, // Pimpinan
            'name' => 'Pimpinan Laundry',
            'email' => 'pimpinan@laundry.com',
            'password' => Hash::make('password')
        ]);
    }
}
