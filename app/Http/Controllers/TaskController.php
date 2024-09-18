<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskService $taskService;
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }
    public function get_tasks($place_id)
    {
        return $this->taskService->tasks($place_id);
    }
    public function get_task(Request $request)
    {        
        return $this->taskService->task($request->task_id);
    }
    public function save_task(TaskRequest $request)
    {

        return $this->taskService->save($request->validated());
    }
}
