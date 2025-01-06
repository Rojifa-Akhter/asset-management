<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create an admin user
         User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'role' => 'Super Admin',
            'address' => 'Dhaka, Bangladesh',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'role' => 'User',
            'address' => 'Dhaka, Bangladesh',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Support Agent',
            'email' => 'supportAgent@gmail.com',
            'role' => 'Support Agent',
            'address' => 'Dhaka, Bangladesh',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Location Employee',
            'email' => 'locationEmployee@gmail.com',
            'role' => 'Location Employee',
            'address' => 'Dhaka, Bangladesh',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Third Party',
            'email' => 'thirdParty@gmail.com',
            'role' => 'Third Party',
            'address' => 'Dhaka, Bangladesh',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Organization',
            'email' => 'Organization@gmail.com',
            'role' => 'Organization',
            'address' => 'Dhaka, Bangladesh',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        User::create([
            'name' => 'Technician',
            'email' => 'Technician@gmail.com',
            'role' => 'Technician',
            'address' => 'Dhaka, Bangladesh',
            'password' => Hash::make('12345678'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
