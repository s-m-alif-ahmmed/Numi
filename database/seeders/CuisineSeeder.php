<?php

namespace Database\Seeders;

use App\Models\Cuisine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CuisineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cuisines = [
            [
                'name' => 'African',
                'slug' => 'african',
            ],
            [
                'name' => 'Asian',
                'slug' => 'asian',
            ],
            [
                'name' => 'American',
                'slug' => 'american',
            ],
            [
                'name' => 'British',
                'slug' => 'british',
            ],
            [
                'name' => 'Cajun',
                'slug' => 'cajun',
            ],
            [
                'name' => 'Caribbean',
                'slug' => 'caribbean',
            ],
            [
                'name' => 'Chinese',
                'slug' => 'chinese',
            ],
            [
                'name' => 'Eastern European',
                'slug' => 'eastern European',
            ],
            [
                'name' => 'European',
                'slug' => 'european',
            ],
            [
                'name' => 'French',
                'slug' => 'french',
            ],
            [
                'name' => 'German',
                'slug' => 'german',
            ],
            [
                'name' => 'Greek',
                'slug' => 'greek',
            ],
            [
                'name' => 'Indian',
                'slug' => 'indian',
            ],
            [
                'name' => 'Irish',
                'slug' => 'irish',
            ],
            [
                'name' => 'Italian',
                'slug' => 'italian',
            ],
            [
                'name' => 'Japanese',
                'slug' => 'japanese',
            ],
            [
                'name' => 'Jewish',
                'slug' => 'jewish',
            ],
            [
                'name' => 'Korean',
                'slug' => 'korean',
            ],
            [
                'name' => 'Latin American',
                'slug' => 'latin american',
            ],
            [
                'name' => 'Mediterranean',
                'slug' => 'mediterranean',
            ],
            [
                'name' => 'Mexican',
                'slug' => 'mexican',
            ],
            [
                'name' => 'Middle Eastern',
                'slug' => 'middle eastern',
            ],
            [
                'name' => 'Nordic',
                'slug' => 'nordic',
            ],
            [
                'name' => 'Southern',
                'slug' => 'southern',
            ],
            [
                'name' => 'Spanish',
                'slug' => 'spanish',
            ],
            [
                'name' => 'Thai',
                'slug' => 'thai',
            ],
            [
                'name' => 'Vietnamese',
                'slug' => 'vietnamese',
            ],
        ];

        foreach ($cuisines as $data) {
            Cuisine::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);
        }
    }
}
