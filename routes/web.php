<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    echo 'test';
});


Route::get('/success', function () {
    return view('changesuccess');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/changepassword','changepassword');
    Route::post('/passwordchange','passwordchange')->name('passwordchange');
    
});