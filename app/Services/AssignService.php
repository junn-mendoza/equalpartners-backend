<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Models\Invite;
use App\Models\Assignee;
use App\Models\TaskUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\TaskListingService;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AssigneeResource;

class AssignService
{
    public function showAssignee(): JsonResponse
    {
        $user = Auth::user();
        $id = $user->id;

        // Fetch all assignees where taskowner_id equals the authenticated user's ID, including the related user info
        $tmpAssignees = Assignee::with(['user','place.tasks'])
            ->where('taskowner_id', $id)
            ->where('place_id', $user->place_id)
            ->get();

        $assignees =  AssigneeResource::collection($tmpAssignees)->toArray(request());

        // Fetch tasks where isDone = 1 for users in the same place
        $places = Task::with(['users' => function ($query) {
           // $query->where('task_users.isDone', 1);  // Filter users where the task is marked as done
            }, 'frequencies', 'categories'])
            ->where('place_id', $user->place_id)  // Assuming you are filtering tasks by place_id
            ->whereHas('users', function ($query) {
                //$query->where('task_users.isDone', 1);  // Ensure tasks have users with isDone = 1
            })
            ->get();
        $taskCount = count($places);    
        //return response()->json($assignees);
        $tmpRewards = Task::with(['users', 'frequencies', 'categories'])
            ->where('place_id', $user->place_id)  // Assuming you are filtering tasks by place_id
            ->get();

        
        
        $tasks = new TaskListingService();    
        $percentages = $tasks->buildAssignee($places); // This is your $percentage data
        //return response()->json($percentages);
        $rewards = $tasks->buildReward($tmpRewards);
            
        // Group rewards by user_id for easier lookup
        $groupedRewards = collect($rewards)->groupBy('user_id')->map(function ($rewardsForUser) {
            $totalTasks = count($rewardsForUser);  // Total number of tasks
            $completedTasks = $rewardsForUser->where('isDone', 1)->count();  // Count of completed tasks
            // Calculate reward percentage (completed tasks / total tasks)
            return $totalTasks > 0 ? ($completedTasks / $totalTasks) : 0;
        });
        // Initialize an empty array to store the final result
        $finalAssignees = [];
        $isReward = false;
        foreach($percentages as $percentage) {
            if(isset($percentage['reward'])) {
                $isReward = true;
            }
        }

        
        //dd($reward_percentage!=0);
        // Loop through the assignees and match the percentage and reward from $percentages and $groupedRewards
        $reward_percentage = (isset($percentages['reward'])?0: 1/count($assignees));
        foreach ($assignees as $assignee) {
            // Find the corresponding percentage for the current user
            $percentageData = collect($percentages)->firstWhere('user_id', $assignee['user_id']);
            //dd($percentageData);
            $percentage = $percentageData['percentage'] ?? 0;  // If percentage is found, use it; otherwise default to 0
            if(!$isReward) {
                //dd(1);
                $reward = 1/count($assignees); 
            } else {
                //dd(345);
                $reward = $groupedRewards[$assignee['user_id']] ?? 0;    
            }
            // Find the corresponding reward for the current user
            //$reward = $groupedRewards[$assignee['user_id']] ?? $reward_percentage;  // If reward is found, use it; otherwise default to 0

            // Add the percentage and reward to the assignee data
            $finalAssignees[] = array_merge($assignee, [
                'percentage' => $percentage,
                'reward' => $reward  // Reward is already a percentage (e.g., 0.5 for 50%)
            ]);
        }

        if(count($rewards) === 0 ) {
            $total = count($assignees);
            $tmp = [];
            foreach($finalAssignees as $finalAssignee) {
                $finalAssignee['reward'] = 1/$total;
                $tmp[] = $finalAssignee;
            }
            $finalAssignees = $tmp;
        }
        
        // Return the final result as JSON
        return response()->json($finalAssignees, 200);
        //return response()->json(AssigneeResource::collection($finalAssignees), 200);
    }

    public function add($data)
    {
        Assignee::create([
            'taskowner_id'=> $data['id'],
            'user_id'=> $data['user_id'],
            'place_id'=> $data['place_id'],
        ]);

        Invite::where('email', $data['email'])->delete();
        return response()->json('Assignee added successfully', 200);
    }
    public function remove($user_id): JsonResponse
    {
        $id = Auth::id();
        //Log::info('Delete Assignee - ' . $user_id);
        // Fetch all assignees where taskowner_id equals the authenticated user's ID, including the related user info
        $deletedRows = Assignee::where('user_id', $user_id)
            ->where('taskowner_id', $id)->delete();

        TaskUser::where('user_id', $user_id)->delete();
        if ($deletedRows > 0) {
            // Records were deleted
            return response()->json("Successfully deleted $deletedRows record(s).", 200);
        }
        return response()->json("No records found to delete. Auth id - $id user_id - $user_id", 200);
    }
}
