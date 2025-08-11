<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\SocialLoginRequest;
use Modules\Auth\Services\Contracts\SocialLoginServiceInterface;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Auth\Transformers\UserResource;

class SocialLoginController extends Controller
{
    public function __construct(private SocialLoginServiceInterface $socialLoginService) {}

    /**
     * @OA\Post(
     *     path="/api/auth/{provider}/callback",
     *     summary="Handle social login callback",
     *     description="Authenticate user via social provider (GitHub/Google) and return user data with JWT token",
     *     tags={"Social Login"},
     *
     *     @OA\Parameter(
     *         name="provider",
     *         in="path",
     *         required=true,
     *         description="Social provider name",
     *
     *         @OA\Schema(
     *             type="string",
     *             enum={"github", "google"}
     *         ),
     *         example="github"
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"code"},
     *
     *             @OA\Property(
     *                 property="code",
     *                 type="string",
     *                 description="Authorization code returned from social provider",
     *                 example="4/0AeaYSHB..."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful authentication",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="User logged in successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/UserResource"
     *             ),
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid provider or code"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="code",
     *                     type="array",
     *
     *                     @OA\Items(type="string", example="The code field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="UserResource",
     *     type="object",
     *
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="name",
     *         type="string",
     *         example="John Doe"
     *     ),
     *     @OA\Property(
     *         property="email",
     *         type="string",
     *         example="john@example.com"
     *     ),
     *     @OA\Property(
     *         property="avatar",
     *         type="string",
     *         example="https://example.com/avatar.jpg"
     *     )
     * )
     */
    public function handleCallback(SocialLoginRequest $request, $provider)
    {
        $code = $request->validated()['code'];

        if ($provider == 'github') {
            $accessToken = $this->socialLoginService->getGithubAccessToken($code);
        } elseif ($provider == 'google') {
            $accessToken = $this->socialLoginService->getGoogleAccessToken(urldecode($code));
        }

        if (! $accessToken) {
            return response()->json(['error' => 'Invalid provider or code'], 400);
        }

        $response = $this->socialLoginService->socialLogin($provider, $accessToken);

        return ResponseBuilder::success(
            new UserResource($response['user']),
            'User logged in successfully.',
            $response['token']
        );
    }
}
