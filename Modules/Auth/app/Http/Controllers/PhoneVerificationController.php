<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\PhoneRequest;
use Modules\Auth\Services\Contracts\PhoneVerificationInterface;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Auth\Transformers\PhoneVerificationResource;
use Modules\Auth\Transformers\UserResource;

class PhoneVerificationController extends Controller
{
    public function __construct(private PhoneVerificationInterface $phoneVerification) {}

    /**
     * Send phone verification code to the authenticated user's phone number.
     *
     * @OA\Post(
     *     path="/api/phone/send-code",
     *     summary="Send phone verification code",
     *     tags={"Phone Verification"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Verification code sent successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The message has been sent successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="username", type="string", example="john_doe"),
     *                     @OA\Property(property="phone", type="string", example="09123456789")
     *                 ),
     *                 @OA\Property(property="code", type="string", example="123456"),
     *                 @OA\Property(property="expires_at", type="string", example="5 minutes from now"),
     *                 @OA\Property(property="created_at", type="string", example="just now"),
     *                 @OA\Property(property="updated_at", type="string", example="just now")
     *             )
     *         )
     *     )
     * )
     */
    public function sendCode()
    {
        $response = $this->phoneVerification->send();

        return ResponseBuilder::success(
            new PhoneVerificationResource($response),
            'The message has been sent successfully.'
        );
    }

    /**
     * Verify the code sent to the authenticated user's phone.
     *
     * @OA\Post(
     *     path="/api/phone/verify-code",
     *     summary="Verify phone verification code",
     *     tags={"Phone Verification"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"code"},
     *
     *             @OA\Property(property="code", type="string", example="123456")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Phone number successfully verified",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your phone number has been successfully verified."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="username", type="string", example="john_doe"),
     *                 @OA\Property(property="phone", type="string", example="09123456789"),
     *                 @OA\Property(property="phone_verified_at", type="string", format="date-time", example="2025-08-03T14:25:00Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Invalid or expired code",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The verification code you entered is invalid or expired.")
     *         )
     *     )
     * )
     */
    public function verifyCode(PhoneRequest $request)
    {
        $data = $request->validated();
        $response = $this->phoneVerification->verify($data);

        if ($response) {
            return ResponseBuilder::success(new UserResource($response), 'Your phone number has been successfully verified.');
        }

        return ResponseBuilder::error('The verification code you entered is invalid or expired.');
    }
}
