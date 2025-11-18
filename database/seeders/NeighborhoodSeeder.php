<?php

namespace Database\Seeders;

use App\Models\Neighborhood;
use Illuminate\Database\Seeder;

class NeighborhoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $neighborhoods = [
            'Sidi El Houari',
            'El Hamri',
            'Medina Jedida',
            'Hai El Amir',
            'Hai Akid Lotfi',
            'Hai Essabah',
            'Hai El Makkari',
            'Hai M\'daghri',
            'Les Castors',
            'Saint-Hubert',
            'Bouamama',
            'Hai Djamel',
            'Hai Yasmine',
            'Pepiniere',
            'Delmonte',
            'Victor Hugo',
        ];

        foreach ($neighborhoods as $neighborhood) {
            Neighborhood::create([
                'name' => $neighborhood,
                'city' => 'Oran',
            ]);
        }
    }
}
