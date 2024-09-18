<?php

namespace App\Services;

use App\Models\User;
use App\Models\Assignee;
use App\Models\TaskUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AssigneeResource;

class AssignService
{
    public function showAssignee(): JsonResponse
    {
        $id = Auth::id();

        // Fetch all assignees where taskowner_id equals the authenticated user's ID, including the related user info
        $assignees = Assignee::with('user')->where('taskowner_id', $id)->get();

        return response()->json(AssigneeResource::collection($assignees), 200);
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
