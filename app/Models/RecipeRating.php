<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'taste_rating',
        'effort_rating',
        'time_rating',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'recipe_id' => 'integer',
        'taste_rating' => 'integer',
        'effort_rating' => 'integer',
        'time_rating' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
