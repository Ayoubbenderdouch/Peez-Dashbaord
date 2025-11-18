<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'uuid' => Str::uuid(),
            'name' => 'Admin User',
            'phone' => '+213551234567',
            'email' => 'admin@peez.dz',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_vendor' => false,
        ]);

        // Manager user
        User::create([
            'uuid' => Str::uuid(),
            'name' => 'Manager User',
            'phone' => '+213551234568',
            'email' => 'manager@peez.dz',
            'password' => Hash::make('password123'),
            'role' => 'manager',
            'is_vendor' => false,
        ]);

        // Vendor user
        User::create([
            'uuid' => Str::uuid(),
            'name' => 'Vendor User',
            'phone' => '+213551234569',
            'email' => 'vendor@peez.dz',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
            'is_vendor' => true,
        ]);

        // Customer users
        User::create([
            'uuid' => Str::uuid(),
            'name' => 'Customer User',
            'phone' => '+213551234561',
            'email' => 'customer@peez.dz',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'is_vendor' => false,
        ]);

        for ($i = 2; $i <= 3; $i++) {
            User::create([
                'uuid' => Str::uuid(),
                'name' => "Customer {$i}",
                'phone' => "+21355123456{$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'is_vendor' => false,
            ]);
        }
    }
}
