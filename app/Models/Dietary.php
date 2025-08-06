<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dietary extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status'
    ];

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'status' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function mealSummery()
    {
        return $this->hasOne(MealSummery::class, 'diet_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_dietaries');
    }
}
