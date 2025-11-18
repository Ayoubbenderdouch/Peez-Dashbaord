<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Neighborhood;
use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $neighborhoods = Neighborhood::all();
        $categories = Category::all();

        $shopNames = [
            'grocery' => ['Super Marché Al Baraka', 'Épicerie El Ihsane', 'Magasin Essalem'],
            'butcher' => ['Boucherie El Amir', 'Boucherie Al Andalous', 'Boucherie Centrale'],
            'patisserie' => ['Pâtisserie Les Délices', 'Pâtisserie La Fontaine', 'Pâtisserie Royal'],
            'female-boutique' => ['Boutique Nour', 'Boutique Zahra', 'Boutique Amira'],
            'male-boutique' => ['Boutique Hassan', 'Boutique Elite', 'Boutique Modern'],
            'cafeteria' => ['Café des Amis', 'Café du Centre', 'Café El Bahia'],
            'fast-food' => ['Fast Food Star', 'Quick Burger', 'Pizza Palace'],
            'fruits-vegetables' => ['Fruits & Légumes Frais', 'Le Jardin', 'Marché Vert'],
            'kiosk' => ['Kiosque du Coin', 'Kiosque Central', 'Kiosque Express'],
            'restaurant' => ['Restaurant El Bahia', 'Restaurant La Perle', 'Restaurant Le Méditerranéen'],
            'beauty-salon' => ['Salon de Beauté Nour', 'Salon Beauty Star', 'Salon Chic'],
            'hair-salon' => ['Salon de Coiffure Elite', 'Barber Shop Pro', 'Salon Modern'],
        ];

        $counter = 0;
        // Create shops for first 5 neighborhoods with all categories
        foreach ($neighborhoods->take(5) as $neighborhood) {
            foreach ($categories as $category) {
                $slug = $category->slug;
                $names = $shopNames[$slug] ?? ['Shop ' . $category->name];
                $shopName = $names[array_rand($names)] . ' - ' . $neighborhood->name;

                Shop::create([
                    'neighborhood_id' => $neighborhood->id,
                    'category_id' => $category->id,
                    'name' => $shopName,
                    'discount_percent' => rand(500, 800) / 100, // 5.00 to 8.00
                    'lat' => 35.6969 + (rand(-100, 100) / 10000),
                    'lng' => -0.6331 + (rand(-100, 100) / 10000),
                    'phone' => '+213' . rand(500000000, 599999999),
                    'is_active' => true,
                ]);

                $counter++;
            }
        }

        // Create partial shops for remaining neighborhoods (only some categories)
        foreach ($neighborhoods->skip(5) as $neighborhood) {
            $randomCategories = $categories->random(rand(3, 6));

            foreach ($randomCategories as $category) {
                $slug = $category->slug;
                $names = $shopNames[$slug] ?? ['Shop ' . $category->name];
                $shopName = $names[array_rand($names)] . ' - ' . $neighborhood->name;

                Shop::create([
                    'neighborhood_id' => $neighborhood->id,
                    'category_id' => $category->id,
                    'name' => $shopName,
                    'discount_percent' => rand(500, 800) / 100,
                    'lat' => 35.6969 + (rand(-100, 100) / 10000),
                    'lng' => -0.6331 + (rand(-100, 100) / 10000),
                    'phone' => '+213' . rand(500000000, 599999999),
                    'is_active' => rand(0, 10) > 1, // 90% active
                ]);
            }
        }
    }
}
