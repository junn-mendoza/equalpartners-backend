<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    //protected 
    protected $guarded = ['id', 'created_at', 'updated_at'];
    public function task_users()
    {
        return $this->hasMany(TaskUser::class);
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
}