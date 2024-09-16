<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'task_id',
    ];
    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with User
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
