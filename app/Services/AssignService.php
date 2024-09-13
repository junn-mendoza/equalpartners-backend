<?php
namespace App\Services;

use App\Http\Resources\AssigneeResource;
use App\Models\User;
use App\Models\Assignee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AssignService
{
    public function showAssignee(): JsonResponse
    {
        $id = Auth::id();

       // Fetch all assignees where taskowner_id equals the authenticated user's ID, including the related user info
        $assignees = Assignee::with('user')->where('taskowner_id', $id)->get();

        return response()->json(AssigneeResource::collection($assignees), 200);
    }
}