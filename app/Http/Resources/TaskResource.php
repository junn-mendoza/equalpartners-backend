<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'note' => $this->note,
            'hr' => $this->hr,
            'min' => $this->min,
            'duedate' => $this->duedate,
            'reminder' => $this->reminder,
            'repeat' => $this->repeat,
            'timeframe' => $this->timeframe,
            'assignees' => $this->task_users->map(function ($taskUser) {
                return [
                    'id' => $taskUser->user->id,
                    'name' => $taskUser->user->name,
                    'email' => $taskUser->user->email,
                ];
            }),
            'frequencies' => $this->frequencies->map(function ($frequency) {
                return [
                    'id' => $frequency->id,
                    'task_id' => $frequency->task_id,
                    'frequent' => $frequency->frequent,
                ];
            }),
            'categories' => $this->categories->map(function ($category) {
                return [
                    'name' => $category->name,
                    'color' => $category->color,
                    'icon' => $category->icon,
                    'category_id' => $category->id,
                    'custom_name' => $category->pivot->custom_name,
                    'pivot_color' => $category->pivot->color,
                ];
            }),
        ];
    }
}
