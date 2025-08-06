<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'image',
        'status'
    ];

    protected $casts = [
        'user_id'   => 'integer',
        'name'      => 'string',
        'image'     => 'string',
        'status'    => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = ['recipe_counts'];

    public function getImageAttribute($value){
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

    public function getRecipeCountsAttribute()
    {
        return $this->collection_recipes()->count();
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function collection_recipes()
    {
        return $this->hasMany(CollectionRecipe::class);
    }

}
