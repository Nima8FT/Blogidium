<?php

use Illuminate\Support\Facades\Route;
use Modules\Tag\Http\Controllers\TagController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tags', TagController::class)->names('tag');
});
