<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;
    protected $fillable = ['place_id', 'description'];

    public function user_rewards()
    {
        return $this->hasMany(UserReward::class);
    }
}
