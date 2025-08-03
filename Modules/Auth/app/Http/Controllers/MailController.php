<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Modules\Auth\Services\ResponseBuilder;

class MailController extends Controller
{
    public function sendNotificationMail(Request $request) {
        $request->user()->SendEmailVerificationNotification();
        return ResponseBuilder::success(
            $request->user()->email,
            'Verification email has been sent successfully.',
        );
    }

    public function verifyMail(EmailVerificationRequest $request) {
        $request->fulfill();
        return ResponseBuilder::success(
            $request->user()->email,
            'Your email has been successfully verified.',
        );
    }
}
