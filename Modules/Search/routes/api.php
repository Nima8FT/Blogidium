<?php

use Illuminate\Support\Facades\Route;
use Modules\Search\Http\Controllers\SearchController;

Route::post('search', [SearchController::class, 'search'])->name('search');
