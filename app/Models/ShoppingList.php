<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ingredient_id',
        'ingredient_category_id',
        'unit',
        'quantity',
    ];

    protected $casts = [
        'unit' => 'string',
        'quantity' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
    public function ingredientCategory()
    {
        return $this->belongsTo(IngredientCategory::class);
    }

}
