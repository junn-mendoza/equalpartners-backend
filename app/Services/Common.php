<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;

class Common
{
    public function addAssignee($model, $data, $id)
    {
        // Fetch existing assignees from the model by reward_id
        $existingAssignees = $model::where('reward_id', $id)->pluck('user_id')->toArray();

        // Assignees in the request
        $newAssignees = $data['assignee'];

        // Ensure that the assignees exist in the users table
        $validAssignees = DB::table('users')->whereIn('id', $newAssignees)->pluck('id')->toArray();

        // Find assignees that need to be added (present in $data but not in existing list)
        $assigneesToAdd = array_diff($validAssignees, $existingAssignees);

        // Find assignees that need to be removed (present in existing list but not in $data)
        $assigneesToRemove = array_diff($existingAssignees, $newAssignees);

        // Add valid new assignees
        foreach ($assigneesToAdd as $assignee) {
            $model::create([
                'reward_id' => $id,
                'user_id' => $assignee,
            ]);
        }

        // Remove assignees that no longer exist
        if (!empty($assigneesToRemove)) {
            $model::where('reward_id', $id)
                  ->whereIn('user_id', $assigneesToRemove)
                  ->delete();
        }
    }
}
