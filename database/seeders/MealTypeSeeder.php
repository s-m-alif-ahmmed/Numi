<?php

namespace Database\Seeders;

use App\Models\MealType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MealTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mealTypes = [
            [
                'name' => 'Main Course',
                'slug' => 'main course',
            ],
            [
                'name' => 'Side Dish',
                'slug' => 'side dish',
            ],
            [
                'name' => 'Dessert',
                'slug' => 'dessert',
            ],
            [
                'name' => 'Appetizer',
                'slug' => 'appetizer',
            ],
            [
                'name' => 'Salad',
                'slug' => 'salad',
            ],
            [
                'name' => 'Bread',
                'slug' => 'bread',
            ],
            [
                'name' => 'Breakfast',
                'slug' => 'breakfast',
            ],
            [
                'name' => 'Soup',
                'slug' => 'soup',
            ],
            [
                'name' => 'Beverage',
                'slug' => 'beverage',
            ],
            [
                'name' => 'Sauce',
                'slug' => 'sauce',
            ],
            [
                'name' => 'Marinade',
                'slug' => 'marinade',
            ],
            [
                'name' => 'Fingerfood',
                'slug' => 'fingerfood',
            ],
            [
                'name' => 'Snack',
                'slug' => 'snack',
            ],
            [
                'name' => 'Drink',
                'slug' => 'drink',
            ],
        ];

        foreach ($mealTypes as $data) {
            MealType::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);
        }
    }
}
