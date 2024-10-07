<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Services\TaskListingService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AssignController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\ForfeitController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;

Route::get('/test1', function () {
    $places = App\Models\Task::with(['users', 'frequencies', 'categories'])
        ->where('place_id', 1)  // Assuming you are filtering tasks by place_id
        ->get();

    $tasks = new TaskListingService();
    return response()->json($tasks->buildReward($places), 200);
    return response()->json($tasks->buildAssignee($places), 200);
});

Route::controller(TaskController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/tasks', 'save_task');
        // Route::get('/tasks/{place_id}', 'get_tasks');
        Route::get('/task/{task_id}', 'get_task');

        Route::get('/tasks/{place_id}', function ($place_id) {
            $places =  App\Models\Task::with(['users', 'frequencies', 'categories'])
                ->where('place_id', $place_id)
                ->get();
            //return response()->json($places, 200);
            $tasks = new TaskListingService();
            //return response()->json($places,200);

            $sorted = $tasks->sortTasksByDate($tasks->buildTask($places));
            return response()->json($sorted, 200);
        });

        Route::post('/taskcalendar', 'taskcalendar');
        Route::delete('/tasks', 'delete_task');
    });
});

Route::get('/user', function (Request $request) {
    //new UserResource(
    return new UserResource($request->user()->load('places'));
    //return $request->user()->load('places');
})->middleware('auth:sanctum');

Route::get('/test', function () {
    echo '124';
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AssignController::class)->group(function () {
        Route::get('/assignee', 'assignee');
        Route::delete('/removeassignee', 'removeassignee');
        Route::post('/addassignee', 'addassignee');
    });
});

Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'getcategory');
});

Route::controller(ProfileController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/profile', 'profile');
        Route::post('/homename', 'homename');
        Route::post('/homeaddress', 'homeaddress');
        Route::post('/places', 'places');
        Route::get('/places', 'get_places');
        Route::patch('/places/{place_id}', 'update_place');
        Route::post('/sendinvitation', 'invitation');
        Route::post('/invite', "show_invite");
    });
});


Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/forgotpassword', 'forgotpassword');
    Route::post('/passwordchange', 'passwordchange');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', 'logout');
    });
});

Route::controller(RewardController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/rewards', 'add');
        Route::delete('/rewards', 'delete');
        Route::get('/rewards/{place_id}', 'get_reward');
        Route::get('/rewards/{place_id}/{reward_id}', 'get_reward_id');
    });
});

Route::controller(ForfeitController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/forfeits', 'add');
        Route::delete('/forfeits', 'delete');
        Route::get('/forfeits/{place_id}', 'get_forfeit');
        Route::get('/forfeits/{place_id}/{forfeit_id}', 'get_forfeit_id');
    });
});


// Route::get('/test1', function () {
//     $calendar = App\Models\Task::with(['users', 'frequencies', 'categories'])
//         ->where('place_id', 1)  // Assuming you are filtering tasks by place_id
//         ->get();
//     $tasks = new TaskListingService();
//     $newCal = $tasks->buildCalendar($calendar);
//     //return response()->json($calendar);
//     return response()->json($newCal);
// });
