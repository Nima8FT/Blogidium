<?php

namespace Modules\Profile\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Article\Models\Article;
use Modules\Article\Transformers\ArticleResource;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Profile\Http\Requests\ProfileRequest;
use Modules\Profile\Services\Contracts\ProfileServiceInterface;
use Modules\Profile\Transformers\ProfileResource;

class ProfileController extends Controller
{
    private $user;

    public function __construct(private ProfileServiceInterface $profileService)
    {
        $this->user = auth()->user();
    }

    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get user profile",
     *     description="Retrieve the authenticated user's profile information.",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your profile has been retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="profile_photo", type="string", example="/storage/profiles/photo.jpg"),
     *                 @OA\Property(property="bio", type="string", example="Short bio..."),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-15T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-15T12:10:00Z")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function showProfile()
    {
        return ResponseBuilder::success(
            new ProfileResource($this->user),
            'Your profile has been retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/profile",
     *     summary="Update user profile",
     *     description="Update the authenticated user's profile information (name, email, username, phone).",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *
     *             @OA\Schema(
     *
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="username", type="string", example="john_doe"),
     *                 @OA\Property(property="phone", type="string", example="+989123456789"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your profile has been updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="username", type="string", example="john_doe"),
     *                 @OA\Property(property="phone", type="string", example="+989123456789")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=400, description="Failed to update profile"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function updateProfile(ProfileRequest $request)
    {
        $data = $request->validated();
        $data = array_filter($data, fn ($value) => $value !== null && $value !== '');
        $result = $this->profileService->updateProfile($data, $this->user);

        if ($result) {
            return ResponseBuilder::success(
                new ProfileResource($this->user),
                'Your profile has been updated successfully.'
            );
        }

        ResponseBuilder::error(
            'Failed to update your profile.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/profile/library",
     *     summary="Get user's saved articles (library)",
     *     description="Retrieve all articles that the authenticated user has saved.",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Library retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your library has been retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Sample Article Title"),
     *                     @OA\Property(property="slug", type="string", example="sample-article-title"),
     *                     @OA\Property(property="content", type="string", example="This is the content of the article."),
     *                     @OA\Property(property="author_id", type="integer", example=1),
     *                     @OA\Property(property="category_id", type="integer", example=2),
     *                     @OA\Property(property="is_published", type="boolean", example=true),
     *                     @OA\Property(property="published_at", type="string", format="date-time", example="2025-09-15T12:00:00Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-01T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-10T12:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getLibrary()
    {
        $library = $this->user->savedArticles;

        return ResponseBuilder::success(
            $library,
            'Your library has been retrieved successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/profile/myarticles",
     *     summary="Get user's authored articles",
     *     description="Retrieve all articles that the authenticated user has authored, ordered by creation date descending.",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User's authored articles retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Your article list retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Sample Article Title"),
     *                     @OA\Property(property="slug", type="string", example="sample-article-title"),
     *                     @OA\Property(property="content", type="string", example="This is the content of the article."),
     *                     @OA\Property(property="author_id", type="integer", example=1),
     *                     @OA\Property(property="category_id", type="integer", example=2),
     *                     @OA\Property(property="is_published", type="boolean", example=true),
     *                     @OA\Property(property="published_at", type="string", format="date-time", example="2025-09-15T12:00:00Z"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-01T12:00:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-10T12:00:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getMyArticles()
    {
        $articles = Article::where('author_id', $this->user->id)->orderBy('created_at', 'desc')->get();

        return ResponseBuilder::success(
            ArticleResource::collection($articles),
            'Your article list retrieved successfully.'
        );
    }
}
