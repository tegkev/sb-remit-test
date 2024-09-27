<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;


Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);

Route::get('blogs', [BlogController::class, 'index']);
Route::get('blogs/{blog}', [BlogController::class, 'show']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('blogs', [BlogController::class, 'store']);
    Route::match(['PUT', 'PATCH'], 'blogs/{blog}', [BlogController::class, 'update']);
    Route::delete('blogs/{blog}', [BlogController::class, 'destroy']);
});
