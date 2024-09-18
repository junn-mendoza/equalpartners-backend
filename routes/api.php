<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AssignController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Resources\TaskResource;


Route::get('/user', function (Request $request) {
    return $request->user()->load('place');
})->middleware('auth:sanctum');

Route::get('/test', function () {
    echo '124';
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AssignController::class)->group(function () {
        Route::get('/assignee', 'assignee');
        Route::delete('/removeassignee', 'removeassignee');
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
    });
});

Route::controller(TaskController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/tasks/{place_id}', 'get_tasks');
        Route::post('/tasks', 'save_task');
        Route::post('/task', 'get_task');
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
