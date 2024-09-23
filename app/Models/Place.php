<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_places')->withTimestamps();
    }
}
