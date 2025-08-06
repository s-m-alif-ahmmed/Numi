<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'time',
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
        return $this->hasOne(MealSummery::class);
    }
}
