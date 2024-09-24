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
    public function save_task(TaskRequest $request)
    {
        return $this->taskService->save($request->validated());
    }
    public function get_tasks($task_id)
    {
        return $this->taskService->task($task_id);
    }
    public function get_task(Request $request)
    {
        return $this->taskService->task($request->task_id);
    }
}
