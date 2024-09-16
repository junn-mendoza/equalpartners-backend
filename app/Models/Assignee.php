<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignee extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'taskowner_id',

    ];

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // 'user_id' is the foreign key in the assignees table
    }
}