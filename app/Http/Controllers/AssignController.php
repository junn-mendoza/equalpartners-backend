<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddAssigneeRequest;
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

    public function addassignee(AddAssigneeRequest $request)
    {
        return $this->assignService->add($request->validated());
    }
}
