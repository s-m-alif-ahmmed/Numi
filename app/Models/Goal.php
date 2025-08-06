<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function mealSummery()
    {
        return $this->hasOne(MealSummery::class, 'goal_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_goals');
    }
}
