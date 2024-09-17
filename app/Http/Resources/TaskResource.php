<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'note' => $this->note,
    //         'hr' => $this->hr,
    //         'min' => $this->min,
    //         'duedate' => $this->duedate,
    //         'reminder' => $this->reminder,
    //         'repeat' => $this->repeat,
    //         'timeframe' => $this->timeframe,
    //         'created_at' => $this->created_at,
    //         'stringDate' => $this->formatCreatedAt($this->created_at), 
    //         'assignees' => $this->task_users->map(function ($taskUser) {
    //             return [
    //                 'id' => $taskUser->user->id,
    //                 'name' => $taskUser->user->name,
    //                 'email' => $taskUser->user->email,
    //             ];
    //         }),
    //         'frequencies' => $this->frequencies->map(function ($frequency) {
    //             return [
    //                 'id' => $frequency->id,
    //                 'task_id' => $frequency->task_id,
    //                 'frequent' => $frequency->frequent,
    //             ];
    //         }),
    //         'categories' => $this->categories->map(function ($category) {
    //             return [
    //                 'name' => $category->name,
    //                 'color' => $category->color,
    //                 'icon' => $category->icon,
    //                 'category_id' => $category->id,
    //                 'custom_name' => $category->pivot->custom_name,
    //                 'pivot_color' => $category->pivot->color,
    //             ];
    //         }),
    //     ];
    // }
    $tasks = [];

    // Loop through the next 7 days
    for ($i = 0; $i < 7; $i++) {
        $date = Carbon::now()->addDays($i); // Get the date for each of the next 7 days
        $formattedDate = $this->formatCreatedAt($date);

        if ($this->created_at->isSameDay($date)) {
            // Task is available on this day
            $tasks[] = [
                'id' => $this->id,
                'name' => $this->name,
                'note' => $this->note,
                'hr' => $this->hr,
                'min' => $this->min,
                'duedate' => $this->duedate,
                'reminder' => $this->reminder,
                'repeat' => $this->repeat,
                'timeframe' => $this->timeframe,
                'created_at' => $this->created_at,
                'stringDate' => $formattedDate,
                'string_comment' => 'Task is available',
                'assignees' => $this->task_users->map(function ($taskUser) {
                    return [
                        'user_id' => $taskUser->user->id,
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
        } else {
            // No task available on this day
            $tasks[] = [
                'stringDate' => $formattedDate,
                'string_comment' => 'No tasks available',
            ];
        }
    }
    return $tasks;
    }
    // Helper function to format 'created_at'
    private function formatCreatedAt($createdAt)
    {
        $date = Carbon::parse($createdAt);

        if ($date->isToday()) {
            return 'Today';
        } elseif ($date->isTomorrow()) {
            return 'Tomorrow';
        } else {
            return $date->format('l, d F'); // Example: "Wednesday, 18 September"
        }
    }
}
