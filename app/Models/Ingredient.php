<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'ingredient_api_id',
        'name',
        'aisel',
        'consistency',
        'original_name',
        'meta',
        'measures',
        'amount',
        'unit',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function ingredientCategory()
    {
        return $this->belongsTo(IngredientCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }

}
