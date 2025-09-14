<?php

namespace Modules\Auth\Http\Controllers;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Exception;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Services\Contracts\AuthServiceInterface;
use Modules\Auth\Services\Contracts\TwoFAServiceInterface;
use Modules\Auth\Services\LoginFieldDetector;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Auth\Transformers\UserResource;

class AuthController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService,
        private LoginFieldDetector $loginFieldDetector,
        private TwoFAServiceInterface $twoFAService,
    ) {}

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name","username","email","password","password_confirmation"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = $this->authService->register($data);

        return ResponseBuilder::success(
            new UserResource($user),
            'User created successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="User login",
     *     description="Authenticate user by username/email/phone and password, returns user data and JWT token.",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"login","password"},
     *
     *             @OA\Property(property="login", type="string", description="Username, email, or phone number of the user", example="johndoe"),
     *             @OA\Property(property="password", type="string", description="User password", example="password")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged in successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *             ),
     *             @OA\Property(property="token", type="string", description="JWT authentication token", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid login credentials",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials. Please check your username/email/phone and password.")
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            $this->loginFieldDetector->detect($request->input('login')) => $request->validated()['login'],
            'password' => $request->validated()['password'],
        ];

        $result = $this->authService->login($credentials);

        if (! $result) {
            return ResponseBuilder::error('Invalid credentials. Please check your username/email/phone and password.');
        }

        $twoFA = $this->twoFAService->checkTwoFA($result['user']);

        if (! $twoFA) {
            return ResponseBuilder::success(
                new UserResource($result['user']),
                'User logged in successfully.',
                $result['token']
            );
        }

        return ResponseBuilder::success(
            new UserResource($result['user']),
            'Two-Factor Authentication (2FA) is enabled. Please enter the 6-digit verification code from your authenticator app to complete login.',
            $twoFA->temp_token
        );
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout user",
     *     description="Logs out the authenticated user by invalidating the JWT token.",
     *     operationId="logoutUser",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User logged out successfully."),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - invalid or missing token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not logged out.")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            $response = $this->authService->logout();
            if ($response) {
                return ResponseBuilder::success(null, 'User logged out successfully.');
            }
        } catch (Exception $err) {
            return ResponseBuilder::error('User not logged out.');
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/delete-account",
     *     summary="Delete the authenticated user's account",
     *     description="Logs out the user by invalidating the JWT token and deletes the authenticated user's account.",
     *     operationId="deleteAccount",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully."),
     *             @OA\Property(property="data", type="null", nullable=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - invalid or missing token",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete user account",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="User not deleted.")
     *         )
     *     )
     * )
     */
    public function deleteAccount()
    {
        $this->authService->logout();
        $user = $this->authService->deleteAccount();

        if (! $user) {
            return ResponseBuilder::error('User not deleted.');
        }

        return ResponseBuilder::success(null, 'User deleted successfully.');
    }

    public function profile()
    {
        return 'i am in';
    }
}
