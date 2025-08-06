<?php

namespace Database\Seeders;

use App\Models\Occasion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OccasionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occasions = [
            [
                'name' => 'Fall',
                'slug' => 'fall',
            ]
        ];

        foreach ($occasions as $data) {
            Occasion::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);
        }
    }
}
