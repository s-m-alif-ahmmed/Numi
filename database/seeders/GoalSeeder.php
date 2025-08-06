<?php

namespace Database\Seeders;

use App\Models\Goal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goals = [
            [
                'name' => 'Lose weight',
                'slug' => 'lose weight',
            ],
            [
                'name' => 'Build muscle',
                'slug' => 'build muscle',
            ],
            [
                'name' => 'Eat healthier',
                'slug' => 'eat healthier',
            ],
            [
                'name' => 'Maintain weight',
                'slug' => 'maintain weight',
            ],
            [
                'name' => 'Improve energy',
                'slug' => 'improve energy',
            ],
            [
                'name' => 'No Preference',
                'slug' => 'no preference',
            ],
        ];

        foreach ($goals as $data) {
            Goal::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);
        }
    }
}
