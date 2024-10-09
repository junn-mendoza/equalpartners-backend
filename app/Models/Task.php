<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    //protected 
    protected $guarded = ['id', 'created_at', 'updated_at'];
    // public function task_users()
    // {
    //     return $this->hasMany(TaskUser::class);
    // }

    // Define many-to-many relationship with User via TaskUser
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_users')
            ->withPivot('user_id', 'task_id','isDone')
            ->withTimestamps();
    }

    public function frequencies()
    {
        return $this->hasMany(Frequent::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'task_categories')
            ->withPivot('custom_name', 'color');
    }

    // Define relationship with Place (assuming tasks belong to a place)
    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    /**
     * Scope to filter by assignees and categories if provided.
     */
    public function scopeFilter($query, $data)
    {
        // Filter by place_id
        if (!empty($data['place_id'])) {
            $query->where('place_id', $data['place_id']);
        }

        // Apply assignee filter if provided
        if (!empty($data['assignee'])) {
            $query->whereHas('users', function ($query) use ($data) {
                $query->whereIn('users.id', $data['assignee']);
            });
        }

        // Apply category filter if provided
        if (!empty($data['categories'])) {
            $query->whereHas('categories', function ($query) use ($data) {
                $query->whereIn('categories.id', $data['categories']);
            });
        }

        return $query;
    }
}
