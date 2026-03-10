<?php

use App\Http\Controllers\StreakController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StreakController::class, 'index']);
Route::post('/streak/update', [StreakController::class, 'store']);
Route::post('/streak/tasks/update', [StreakController::class, 'updateTasks']);
Route::post('/streak/tasks/save', [StreakController::class, 'saveTaskNames']);