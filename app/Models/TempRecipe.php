<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_api_id',
        'title',
        'image_url',
        'source_url',
        'category',
        'preparation_time',
        'cooking_time',
        'total_ready_time',
        'servings',
        'description',
        'instruction',
        'calories',
        'protein',
        'fat',
        'carbs',
    ];

}
