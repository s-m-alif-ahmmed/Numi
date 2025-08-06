<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealSummery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'goal_id',
        'time_id',
        'diet_id',
        'region_id',
    ];

    protected $casts = [
        'goal_id' => 'integer',
        'time_id' => 'integer',
        'diet_id' => 'integer',
        'region_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function time()
    {
        return $this->belongsTo(TimeAvailability::class, 'time_id');
    }

    public function dietary()
    {
        return $this->belongsTo(Dietary::class, 'diet_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
