<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\Http\Controllers\ProfileController;

Route::middleware('jwt.auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'showProfile'])->name('profile.show');
    Route::post('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
});
