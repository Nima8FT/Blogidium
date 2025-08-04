<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Modules\Auth\Services\ResponseBuilder;

class MailController extends Controller
{
    /**
     * Send a verification email to the authenticated user's email address.
     *
     * @OA\Post(
     *     path="/api/email/verification-notification",
     *     summary="Send verification email",
     *     tags={"Email Verification"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Verification email has been sent successfully.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Verification email has been sent successfully."),
     *             @OA\Property(property="data", type="string", example="user@example.com")
     *         )
     *     )
     * )
     */
    public function sendNotificationMail(Request $request)
    {
        $request->user()->SendEmailVerificationNotification();

        return ResponseBuilder::success(
            $request->user()->email,
            'Verification email has been sent successfully.',
        );
    }

    /**
     * @OA\Post(
     *     path="/api/email/verify/{id}/{hash}",
     *     summary="Verify email address",
     *     tags={"Email Verification"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *         description="Email verification hash (only the hash, without query string)",
     *
     *         @OA\Schema(type="string", example="a410347aabe0039597cb405f771f043d411e4167")
     *     ),
     *
     *     @OA\Parameter(
     *         name="expires",
     *         in="query",
     *         required=true,
     *         description="Expiration timestamp of the verification link",
     *
     *         @OA\Schema(type="integer", example=1754228035)
     *     ),
     *
     *     @OA\Parameter(
     *         name="signature",
     *         in="query",
     *         required=true,
     *         description="Signed hash for verification link",
     *
     *         @OA\Schema(type="string", example="c0ec9a6c0589a488aa5513ef918802f675d85340ce5bfa59efa001f6033bde84")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your email has been successfully verified."),
     *             @OA\Property(property="data", type="string", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized or invalid verification link",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="This action is unauthorized.")
     *         )
     *     )
     * )
     */
    public function verifyMail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return ResponseBuilder::success(
            $request->user()->email,
            'Your email has been successfully verified.',
        );
    }
}
