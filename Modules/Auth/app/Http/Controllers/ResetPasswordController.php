<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Modules\Auth\Http\Requests\ForgotPasswordRequest;
use Modules\Auth\Http\Requests\ResetPasswordRequest;
use Modules\Auth\Services\Contracts\ResetPasswordInterface;
use Modules\Auth\Services\ResponseBuilder;

class ResetPasswordController extends Controller
{
    public function __construct(private ResetPasswordInterface $passwordService) {}

    /**
     * Send password reset link to user's email.
     *
     * Sends a password reset link to the email if the user exists.
     *
     * @OA\Post(
     *     path="/api/forgot-password",
     *     tags={"Reset Password"},
     *     summary="Send password reset link",
     *     description="Send a password reset link to a valid user email address.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Reset link sent successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="A password reset link has been sent to your email."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="email", type="string", example="user@example.com")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Failed to send reset link",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="We were unable to send the password reset link. Please try again later.")
     *         )
     *     )
     * )
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->validated();
        $response = $this->passwordService->forgotPassword($data);
        if ($response === Password::RESET_LINK_SENT) {
            return ResponseBuilder::success($data, 'A password reset link has been sent to your email.');
        }

        return ResponseBuilder::error('We were unable to send the password reset link. Please try again later.');
    }

    /**
     * Reset the user's password.
     *
     * Resets the user's password using the provided token and credentials.
     *
     * @OA\Post(
     *     path="/api/reset-password",
     *     tags={"Reset Password"},
     *     summary="Reset user password",
     *     description="Reset user password by providing token, email, new password, and password confirmation.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "token", "password", "password_confirmation"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="token", type="string", example="your-reset-token"),
     *             @OA\Property(property="password", type="string", format="password", example="NewPassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="NewPassword123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your password has been successfully reset."),
     *             @OA\Property(property="data", type="string", example="user@example.com")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid token or email",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to reset the password. The token may be invalid or expired.")
     *         )
     *     )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        $response = $this->passwordService->resetPassword($data);
        if ($response === Password::PASSWORD_RESET) {
            return ResponseBuilder::success($data['email'], 'Your password has been successfully reset.');
        }

        return ResponseBuilder::error('Failed to reset the password. The token may be invalid or expired.');
    }
}
