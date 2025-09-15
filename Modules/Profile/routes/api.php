<?php

use Illuminate\Support\Facades\Route;
use Modules\Profile\Http\Controllers\ProfileController;

Route::middleware('jwt.auth')->group(function () {
    Route::get('profile', [ProfileController::class, 'showProfile'])->name('profile.show');
    Route::post('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::get('profile/library', [ProfileController::class, 'getLibrary'])->name('profile.library');
    Route::get('profile/myarticles', [ProfileController::class, 'getMyArticles'])->name('profile.my-articles');
});
