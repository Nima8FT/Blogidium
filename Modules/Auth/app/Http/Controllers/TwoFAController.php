<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\VerifyTwoFARequest;
use Modules\Auth\Services\Contracts\TwoFAServiceInterface;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Auth\Transformers\UserResource;
use PragmaRX\Google2FA\Google2FA;

class TwoFAController extends Controller
{
    private $google2fa;

    private $user;

    public function __construct(private TwoFAServiceInterface $twoFAService)
    {
        $this->google2fa = new Google2FA;
        $this->user = auth()->user();
    }

    /**
     * @OA\Post(
     *     path="/api/2fa/enable",
     *     summary="Enable Two-Factor Authentication (2FA) for the authenticated user",
     *     tags={"TwoFA"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="2FA enabled successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Two-factor authentication has been enabled successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="secret", type="string", example="RBA62YCLEKS3VMMI"),
     *                 @OA\Property(property="qr_code", type="string", example="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...")
     *             )
     *         )
     *     )
     * )
     */
    public function enableTwoFA()
    {
        $result = $this->twoFAService->enableTwoFA($this->user, $this->google2fa);

        return ResponseBuilder::success(
            $result,
            'Two-factor authentication has been enabled successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/2fa/disable",
     *     summary="Disable Two-Factor Authentication (2FA) for the authenticated user",
     *     tags={"TwoFA"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="2FA disabled successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Two-factor authentication has been disabled successfully."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function disableTwoFA()
    {
        $result = $this->twoFAService->disableTwoFA($this->user);

        if ($result) {
            return ResponseBuilder::success(
                null,
                'Two-factor authentication has been disabled successfully.'
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/2fa/verify",
     *     summary="Verify the 2FA code for the authenticated user",
     *     tags={"TwoFA"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="token", type="string", example="a1b2c3d4e5f6..."),
     *             @OA\Property(property="otp", type="string", example="123456")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="2FA verified successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Two-factor authentication verified successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
     *             ),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid or expired verification code",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The provided verification code is invalid or expired."),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     )
     * )
     */
    public function verifyTwoFA(VerifyTwoFARequest $request)
    {
        $data = $request->validated();

        $result = $this->twoFAService->verifyTwoFA($data, $this->google2fa);

        if ($result) {
            return ResponseBuilder::success(
                new UserResource($result['user']),
                'Two-factor authentication verified successfully.',
                $result['token']
            );
        }

        return ResponseBuilder::error(
            'The provided verification code is invalid or expired.'
        );
    }
}
