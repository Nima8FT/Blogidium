<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\MailController;

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::delete('delete-account', [AuthController::class, 'deleteAccount'])->name('delete-account');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');

    //email verification send
    Route::post('email/verification-notification', [MailController::class, 'sendNotificationMail'])->middleware(['throttle:5,1'])->name('mail.notification');
    Route::post('email/verify/{id}/{hash}', [MailController::class, 'verifyMail'])->name('verification.verify');
});
