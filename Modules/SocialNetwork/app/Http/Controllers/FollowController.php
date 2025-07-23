<?php

namespace Modules\SocialNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\Auth\Services\ResponseBuilder;
use Modules\SocialNetwork\Services\Contracts\FollowServiceInterface;
use Modules\SocialNetwork\Transformers\FollowResource;

class FollowController extends Controller
{
    public function __construct(private FollowServiceInterface $followService) {}

    /**
     * Follow a user.
     *
     * @OA\Post(
     *     path="/api/follow/{user}",
     *     tags={"Follow"},
     *     summary="Follow a user",
     *     description="Follow a user by user ID",
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID of user to follow",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully followed user",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="username", type="string")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Already following or invalid operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function follow(User $user)
    {
        $response = $this->followService->follow($user);
        if ($response) {
            return ResponseBuilder::success(
                new FollowResource($user),
                'You are now following this user.'
            );
        } else {
            return ResponseBuilder::error(
                'You are already following this user.'
            );
        }

    }

    /**
     * Unfollow a user.
     *
     * @OA\Post(
     *     path="/api/unfollow/{user}",
     *     tags={"Follow"},
     *     summary="Unfollow a user",
     *     description="Unfollow a user by user ID",
     *
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID of user to unfollow",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Successfully unfollowed user",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="username", type="string")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Not following or invalid operation",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function unfollow(User $user)
    {
        $response = $this->followService->unfollow($user);
        if ($response) {
            return ResponseBuilder::success(
                new FollowResource($user),
                'You have unfollowed this user.'
            );
        } else {
            return ResponseBuilder::error(
                'You are not following this user.'
            );
        }
    }

    /**
     * Get the list of users the authenticated user is following.
     *
     * @OA\Get(
     *     path="/api/followings",
     *     tags={"Follow"},
     *     summary="List followings",
     *     description="Get list of users the authenticated user is following",
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of followings",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="username", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function followings()
    {
        $followings = $this->followService->followings();

        return ResponseBuilder::success(
            FollowResource::collection($followings),
            'Here are the users you are following.'
        );
    }

    /**
     * Get the list of users who follow the authenticated user.
     *
     * @OA\Get(
     *     path="/api/followers",
     *     tags={"Follow"},
     *     summary="List followers",
     *     description="Get list of users following the authenticated user",
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of followers",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="username", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function followers()
    {
        $followers = $this->followService->followers();

        return ResponseBuilder::success(
            FollowResource::collection($followers),
            'Here are the users following you.'
        );
    }
}
