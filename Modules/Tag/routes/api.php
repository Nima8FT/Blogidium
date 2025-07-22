<?php

use Illuminate\Support\Facades\Route;
use Modules\Tag\Http\Controllers\TagController;

Route::apiResource('tags', TagController::class)->names('tags');
