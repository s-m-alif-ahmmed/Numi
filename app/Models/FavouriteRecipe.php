<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_id'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'recipe_id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function recipe()
    {
        return $this->hasOne(Recipe::class);
    }

    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
