<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'time',
        'time_name',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }
}
