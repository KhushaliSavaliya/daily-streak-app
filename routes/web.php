<?php

use App\Http\Controllers\StreakController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StreakController::class, 'index']);