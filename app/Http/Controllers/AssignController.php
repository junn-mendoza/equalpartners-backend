<?php

namespace App\Http\Controllers;

use App\Services\AssignService;
use Illuminate\Http\Request;

class AssignController extends Controller
{
    protected AssignService $assignService;
    public function __construct(AssignService $assignService)
    {
        $this->assignService = $assignService;
    }

    public function assignee()
    {
        return $this->assignService->showAssignee();
    }

    public function removeassignee(Request $request)
    {
        return $this->assignService->remove($request->input('user_id'));
    }
}
