<?php

use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\ArticleController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('articles', ArticleController::class)->names('article');
});
