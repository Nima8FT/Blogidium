<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\MailController;
use Modules\Auth\Http\Controllers\PhoneVerificationController;
use Modules\Auth\Http\Controllers\ResetPasswordController;
use Modules\Auth\Http\Controllers\SocialLoginController;
use Modules\Auth\Http\Controllers\TwoFAController;

Route::middleware(['guest'])->group(function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // reset password
    Route::post('/forgot-password', [ResetPasswordController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');

    // social login
    Route::post('/auth/{provider}/callback', [SocialLoginController::class, 'handleCallback'])->name('social.login');

    // two factor authentication
    Route::post('2fa/verify', [TwoFAController::class, 'verifyTwoFA'])->name('2fa.verify');
});

Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::delete('delete-account', [AuthController::class, 'deleteAccount'])->name('delete-account');
    Route::get('profile', [AuthController::class, 'profile'])->name('profile');

    // email verification send
    Route::post('email/verification-notification', [MailController::class, 'sendNotificationMail'])->middleware(['throttle:5,1'])->name('mail.notification');
    Route::post('email/verify/{id}/{hash}', [MailController::class, 'verifyMail'])->name('verification.verify');

    // phone verification
    Route::post('phone/send-code', [PhoneVerificationController::class, 'sendCode'])->name('phone.send-code');
    Route::post('phone/verify-code', [PhoneVerificationController::class, 'verifyCode'])->name('phone.verify-code');

    // two factor authentication
    Route::post('2fa/enable', [TwoFAController::class, 'enableTwoFA'])->name('2fa.enable');
    Route::post('2fa/disable', [TwoFAController::class, 'disableTwoFA'])->name('2fa.disable');
});
