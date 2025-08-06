<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
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

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

}
