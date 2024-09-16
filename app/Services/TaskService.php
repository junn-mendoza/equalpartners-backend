<?php

namespace App\Services;

use Exception;
use App\Models\Task;
use App\Models\Frequent;
use App\Models\TaskUser;
use App\Models\TaskCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SingleTaskResource;

class TaskService
{
    public function save($data)
    {

        $id = Auth::id();
        DB::beginTransaction();
        try {
            $taskData = [
                'name' => $data['title'],
                'hr' => $data['hours'],
                'min' => $data['minutes'],
                "note" => $data['note'],
                "reminder" => $data['reminder'],
                "repeat" => $data['repeat'],
                "timeframe" => $data['timeframe'],
            ];
            if ($data['isAdd']) {
                $task = Task::create($taskData);
            } else {
                $task = Task::where('id', $data['task_id'])->first();
                if ($task) {
                    // dump($taskData);

                    $updated = $task->update($taskData);
                    if ($updated) {
                        // dump($task);
                        // dump('Update success2');
                        // DB::commit();
                    }
                }
            }
            $userIdsToKeep = [];
            foreach ($data['assignee'] as $assignee) {

                TaskUser::UpdateOrCreate(
                    ['task_id' => $task->id,],
                    [
                        'task_id' => $task->id,
                        'user_id' => $assignee['user_id'],
                    ]
                );
                // Add to the list of user IDs to keep
                $userIdsToKeep[] = $assignee['user_id'];
            }
            // Step 2: Delete task-user relationships that are not in the list
            TaskUser::where('task_id', $task->id)
                ->whereNotIn('user_id', $userIdsToKeep)
                ->delete();

            $frequentsToKeep = [];
            foreach ($data['repeatDates'] as $repeat) {

                Frequent::UpdateOrCreate(
                    ['task_id' => $task->id, 'frequent' => $repeat],
                    [
                        'task_id' => $task->id,
                        'frequent' => $repeat
                    ]
                );
                $frequentsToKeep[] = $repeat;
            }
            Frequent::where('task_id', $task->id)
                ->whereNotIn('frequent', $frequentsToKeep)
                ->delete();

            // Step 1: Collect the category IDs that need to be kept
            $categoryIdsToKeep = [];
            foreach ($data['selectedCategories'] as $cat) {

                TaskCategory::UpdateOrCreate(
                    [
                        'task_id' => $task->id,
                        'category_id' => $cat['category_id'],
                    ],
                    [
                        'task_id' => $task->id,
                        'category_id' => $cat['category_id'],
                        'custom_name' => $cat['name'],
                        'color' => $cat['color'],
                    ]
                );
                // Add to the list of category IDs to keep
                $categoryIdsToKeep[] = $cat['category_id'];

                // Step 2: Delete categories that are not in the list
                TaskCategory::where('task_id', $task->id)
                    ->whereNotIn('category_id', $categoryIdsToKeep)
                    ->delete();
            }
            DB::commit();
            return response()->json('Task successfully saved.', 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Module (TaskService) save - " . $e->getMessage());
            return response()->json("Module (TaskService) save - " . $e->getMessage(), 500);
        }
        return response()->json('Error with saving.', 500);
        //return $user;
    }

    public function task($task_id)
    {
        $task = Task::with(['task_users.user', 'frequencies', 'categories'])
            ->whereHas('task_users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id',$task_id)
            ->first();

       
        return response()->json(new SingleTaskResource($task),200 );
    }
    public function tasks()
    {


        $task = Task::with(['task_users.user', 'frequencies', 'categories'])
            ->whereHas('task_users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();

       
        return response()->json(
            [
                'task' => TaskResource::collection($task),
            ],
            200
        );
    }
}
