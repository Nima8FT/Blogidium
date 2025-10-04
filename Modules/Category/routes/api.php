<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;

Route::apiResource('categories', CategoryController::class)->names('category')->middleware('jwt.auth');
