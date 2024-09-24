<?php

namespace App\Services;

use App\Http\Requests\SingleTaskRequest;
use App\Http\Resources\SingleTaskResource;
use Exception;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\Assignee;
use App\Models\Frequent;
use App\Models\TaskCategory;
use App\Models\TaskUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TaskService
{
    
    public function save($data)
    {
        DB::beginTransaction();
        try {
            $task = Task::updateOrCreate([
                'id' => $data['task_id']
            ],[
                'name' => $data['title'],
                'note' => $data['note'],
                'reminder' => $data['reminder'],
                'repeat' => $data['repeat'],
                'timeframe' => $data['timeframe'],
                'duedate' => $data['dueDate'],                
                'hr' => $data['hours'],
                'min' => $data['minutes'],
                'place_id' => $data['place_id'],
            ]);
            TaskUser::where('task_id', $task->id)->delete();
            foreach($data['assignee'] as $assignee) {
                TaskUser::create([
                    'task_id' => $task->id,
                    'user_id' => $assignee['user_id'],
                ]);
            }
            Frequent::where('task_id', $task->id)->delete();
            foreach($data['repeatDates'] as $frequent) {
                Frequent::create([
                    'task_id' => $task->id,
                    'frequent' => $frequent,
                ]);
            }
            TaskCategory::where('task_id', $task->id)->delete();
            foreach($data['selectedCategories'] as $category) {
                TaskCategory::create([
                    'task_id' => $task->id,
                    'category_id' => $category['category_id'],
                    'custom_name' => $category['name'],
                    'color' => $category['color'],
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Module (TaskService) save - " . $e->getMessage());
            return response()->json('Something went wrong.'. $e->getMessage(),400);
        }
        return response()->json('Task created.',201);;
    }

    public function task($task_id)
    {
        $task = Task::with([
                'users','frequencies','categories'
                ])->find($task_id);
        return response()->json(new SingleTaskResource($task),200);
    }
}