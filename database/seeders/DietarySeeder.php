<?php

namespace Database\Seeders;

use App\Models\Dietary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DietarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dietary = [
            [
                'name' => 'Gluten Free',
                'slug' => 'gluten free',
            ],
            [
                'name' => 'Ketogenic',
                'slug' => 'ketogenic',
            ],
            [
                'name' => 'Vegetarian',
                'slug' => 'vegetarian',
            ],
            [
                'name' => 'Lacto-Vegetarian',
                'slug' => 'lacto-vegetarian',
            ],
            [
                'name' => 'Ovo-Vegetarian',
                'slug' => 'ovo-vegetarian',
            ],
            [
                'name' => 'Vegan',
                'slug' => 'vegan',
            ],
            [
                'name' => 'Pescetarian',
                'slug' => 'pescetarian',
            ],
            [
                'name' => 'Paleo',
                'slug' => 'paleo',
            ],
            [
                'name' => 'Primal',
                'slug' => 'primal',
            ],
            [
                'name' => 'Low FODMAP',
                'slug' => 'low fodmap',
            ],
            [
                'name' => 'Whole30',
                'slug' => 'whole30',
            ],
        ];

        foreach ($dietary as $data) {
            Dietary::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);
        }
    }
}
