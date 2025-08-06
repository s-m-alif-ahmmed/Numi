<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_api_id',
        'title',
        'image',
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
        'level',
        'status',
    ];

    protected $casts = [
        'level' => 'string',
        'status' => 'string',
        'created_at_human' => 'string',
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'deleted_at',
        'updated_at',
    ];

    protected $appends = ['average_rating', 'rating_count','like_count', 'comment_count', 'created_at_human', 'is_favourite', 'is_like'];

    public function getImageAttribute($value){
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

    public function getAverageRatingAttribute()
    {
        $ratings = $this->ratings;

        if ($ratings->count() === 0) {
            return 0;
        }

        $total = $ratings->map(function ($rating) {
            $total = ($rating->taste_rating + $rating->effort_rating + $rating->time_rating) / 3;

            return $total;
        });

        return round($total->avg());
    }

    public function getRatingCountAttribute()
    {
        return $this->ratings()->count();
    }

    public function getLikeCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getCommentCountAttribute()
    {
        return $this->comments()->count();
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : null;
    }

    public function getIsFavouriteAttribute()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->favourites()->where('recipe_id', $this->id)->exists();
    }

    public function getIsLikeAttribute()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->likes()->where('recipe_id', $this->id)->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(RecipeRating::class);
    }

    public function cuisine()
    {
        return $this->belongsTo(Cuisine::class);
    }

    public function tags()
    {
        return $this->hasMany(RecipeTag::class);
    }

    public function category()
    {
        return $this->belongsTo(RecipeCategory::class);
    }

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function instructions()
    {
        return $this->hasMany(Instruction::class, 'recipes_id');
    }

    public function favourites()
    {
        return $this->hasMany(FavouriteRecipe::class);
    }

    public function comments()
    {
        return $this->hasMany(RecipeComment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function recipeTags()
    {
        return $this->hasMany(RecipeTag::class);
    }

}
