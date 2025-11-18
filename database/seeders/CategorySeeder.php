<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Grocery',
            'Butcher',
            'Patisserie',
            'Female Boutique',
            'Male Boutique',
            'Cafeteria',
            'Fast Food',
            'Fruits & Vegetables',
            'Kiosk',
            'Restaurant',
            'Beauty Salon',
            'Hair Salon',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
                'slug' => Str::slug($category),
            ]);
        }
    }
}
