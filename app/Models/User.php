<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Tables\Columns\Layout\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'url',
        'bio',
        'youtube',
        'facebook',
        'tiktok',
        'instagram',
        'blog',
        'location',
        'avatar',
        'role',
        'reset_password_token',
        'reset_password_token_exp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = ['is_follow'];

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        if (Auth::user()->role === 'Admin') {
            return true;
        }

        return false;
    }

    public function getAvatarAttribute($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

    public function getIsFollowAttribute()
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return false;
        }

        return UserFollower::where('user_id', $authUser->id )
        ->where('follower_user_id', $this->id)
        ->exists();
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function comments()
    {
        return $this->hasMany(RecipeComment::class);
    }

    public function ratings()
    {
        return $this->hasMany(RecipeRating::class);
    }

    public function userLinks()
    {
        return $this->hasMany(UserLink::class);
    }

    public function mealSummaries()
    {
        return $this->hasMany(MealSummery::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function followers()
    {
        return $this->hasMany(UserFollower::class, 'follower_user_id');
    }

    public function dietaries()
    {
        return $this->belongsToMany(UserDietary::class, 'user_dietaries', 'user_id', 'dietary_id');
    }

    public function goals()
    {
        return $this->belongsToMany(UserGoal::class, 'user_goals', 'user_id', 'goal_id');
    }

    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class);
    }

    public function favourites()
    {
        return $this->belongsToMany(Recipe::class, 'favourite_recipes', 'user_id', 'recipe_id');
    }

}
