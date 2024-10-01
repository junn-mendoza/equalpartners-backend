<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forfeit extends Model
{
    use HasFactory;
    protected $fillable = ['place_id', 'challenges', 'must_complete'];

    public function user_forfeits()
    {
        return $this->hasMany(UserForfeit::class);
    }
}
