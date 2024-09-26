<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignee extends Model
{
    use HasFactory;
    protected $fillable = [
        'place_id',
        'user_id',
        'taskowner_id',
    ];

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class); // 'user_id' is the foreign key in the assignees table
    }

    

    // Define the relationship with Place
    public function place()
    {
        return $this->belongsTo(Place::class); // 'place_id' is the foreign key in the assignees table
    }

    // Define the relationship with Task Owner (who assigns the task)
    public function taskOwner()
    {
        return $this->belongsTo(User::class, 'taskowner_id'); // 'taskowner_id' refers to the task owner (also a user)
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class,        // Final Model we are trying to access
            TaskUser::class,    // Intermediate model (the pivot table that connects users and tasks)
            'user_id',          // Foreign key on TaskUser model that links to User (user_id in task_users table)
            'id',               // Foreign key on Task model (task_id in task_users table)
            'user_id',          // Local key on Assignee model (user_id in assignees table)
            'task_id'           // Local key on TaskUser model (task_id in task_users table)
        );
    }
}
