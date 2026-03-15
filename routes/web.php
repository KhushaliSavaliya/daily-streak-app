<?php

use App\Http\Controllers\StreakController;
use Illuminate\Support\Facades\Route;

// streak routes
Route::get('/', [StreakController::class, 'index']);
Route::post('/streak/update', [StreakController::class, 'store']);
Route::post('/streak/tasks/update', [StreakController::class, 'updateTasks']);
Route::post('/streak/tasks/save', [StreakController::class, 'saveTaskNames']);
Route::post('/streak/shop/buy-freeze', [StreakController::class, 'buyFreeze']);