<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AssignController;
use App\Http\Controllers\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    echo '124';
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AssignController::class)->group(function () {
        Route::get('/assignee','assignee');
    });
});


Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories','getcategory');
});
Route::controller(AuthController::class)->group(function () {
    Route::post('/register','register');
    Route::post('/login','login');
    Route::post('/forgotpassword','forgotpassword');
    Route::post('/passwordchange','passwordchange');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout','logout');
    });

});


