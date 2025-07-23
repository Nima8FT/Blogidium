<?php

namespace Modules\SocialNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Article\Models\Article;
use Modules\Auth\Services\ResponseBuilder;
use Modules\SocialNetwork\Services\Contracts\LikeServiceInterface;

class LikeController extends Controller
{
    public function __construct(private LikeServiceInterface $likeService) {}

    /**
     * @OA\Post(
     *     path="/api/like/{article}",
     *     summary="Like an article",
     *     description="Set like status to true for the given article by authenticated user.",
     *     operationId="likeArticle",
     *     tags={"Likes"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="ID of article to like",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article liked successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="title", type="string", example="My Awesome Article"),
     *                 @OA\Property(property="like", type="string", example="like")
     *             ),
     *             @OA\Property(property="message", type="string", example="You have successfully liked this article.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     */
    public function like(Article $article)
    {
        $data = $this->likeService->setLikeStatus($article, true);

        return ResponseBuilder::success(
            $data,
            'You have successfully liked this article.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/dislike/{article}",
     *     summary="Dislike an article",
     *     description="Set like status to false (dislike) for the given article by authenticated user.",
     *     operationId="dislikeArticle",
     *     tags={"Likes"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="ID of article to dislike",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article disliked successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="title", type="string", example="My Awesome Article"),
     *                 @OA\Property(property="like", type="string", example="dislike")
     *             ),
     *             @OA\Property(property="message", type="string", example="You have successfully disliked this article.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     * )
     */
    public function dislike(Article $article)
    {
        $data = $this->likeService->setLikeStatus($article, false);

        return ResponseBuilder::success(
            $data,
            'You have successfully disliked this article.'
        );
    }
}
