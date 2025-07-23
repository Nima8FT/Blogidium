<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialNetwork\Http\Controllers\FollowController;
use Modules\SocialNetwork\Http\Controllers\LikeController;
use Modules\SocialNetwork\Http\Controllers\SaveController;

Route::middleware('jwt.auth')->group(function () {
    // Follow System
    Route::post('follow/{user}', [FollowController::class, 'follow'])->name('follow');
    Route::post('unfollow/{user}', [FollowController::class, 'unfollow'])->name('unfollow');
    Route::get('followings', [FollowController::class, 'followings'])->name('followings');
    Route::get('followers', [FollowController::class, 'followers'])->name('followers');

    // Like System
    Route::post('like/{article}', [LikeController::class, 'like'])->name('like');
    Route::post('dislike/{article}', [LikeController::class, 'dislike'])->name('dislike');

    // Save System
    Route::post('save/{article}', [SaveController::class, 'save'])->name('save');
    Route::post('unsave/{article}', [SaveController::class, 'unsave'])->name('unsave');
});
