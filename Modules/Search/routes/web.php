<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\Http\Controllers\SearchController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('searches', SearchController::class)->names('search');
});
