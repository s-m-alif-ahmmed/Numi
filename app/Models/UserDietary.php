<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDietary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dietary_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dietary()
    {
        return $this->belongsTo(Dietary::class);
    }
}
