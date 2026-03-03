<?php

use App\Http\Controllers\StreakController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StreakController::class, 'index']);
Route::post('/streak/update', [StreakController::class, 'store']);