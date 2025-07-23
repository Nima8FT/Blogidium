<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialNetwork\Http\Controllers\FollowController;

Route::middleware('jwt.auth')->group(function () {
    Route::post('follow/{user}', [FollowController::class, 'follow'])->name('follow');
    Route::post('unfollow/{user}', [FollowController::class, 'unfollow'])->name('unfollow');
    Route::get('followings', [FollowController::class, 'followings'])->name('followings');
    Route::get('followers', [FollowController::class, 'followers'])->name('followers');
});
