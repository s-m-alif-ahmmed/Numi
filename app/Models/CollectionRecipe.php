<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'recipe_id'
    ];

    protected $casts = [
        'collection_id' => 'integer',
        'recipe_id' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id');
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }
}
