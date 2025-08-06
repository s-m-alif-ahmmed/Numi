<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SearchQuery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'search_query',
        'category_cuisine',
        'preparation_min_time',
        'preparation_max_time',
        'difficulty_level',
    ];

    protected $casts = [
        'input' => 'string',
        'category_cuisine' => 'string',
        'preparation_min_time' => 'string',
        'preparation_max_time' => 'string',
        'difficulty_level' => 'string',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];



}
