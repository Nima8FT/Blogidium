<?php

use Illuminate\Support\Facades\Route;
use Modules\SocialNetwork\Http\Controllers\SocialNetworkController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('socialnetworks', SocialNetworkController::class)->names('socialnetwork');
});
