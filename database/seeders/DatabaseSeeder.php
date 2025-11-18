<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            NeighborhoodSeeder::class,
            CategorySeeder::class,
            UserSeeder::class,
            ShopSeeder::class,
        ]);
    }
}
