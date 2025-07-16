<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::post('register', [AuthController::class, 'register'])->name('register');
