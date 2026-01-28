<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $status = "Started";
    $day = 1;
    return view('streak', compact('status', 'day'));
});