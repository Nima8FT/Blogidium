<?php

use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\ArticleController;

Route::apiResource('articles', ArticleController::class)->names('articles')->middleware(['jwt.auth']);
