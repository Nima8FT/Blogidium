<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\Http\Controllers\SearchController;

Route::middleware('jwt.auth')->group(function () {
    Route::get('search', [SearchController::class, 'search'])->name('search');
});
