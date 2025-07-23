<?php

namespace Modules\SocialNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Article\Models\Article;
use Modules\Auth\Services\ResponseBuilder;
use Modules\SocialNetwork\Services\Contracts\SaveServiceInterface;

class SaveController extends Controller
{
    public function __construct(private SaveServiceInterface $saveService) {}

    /**
     * @OA\Post(
     *     path="/api/save/{article}",
     *     summary="Save an article",
     *     description="Save an article to the authenticated user's saved list",
     *     operationId="saveArticle",
     *     tags={"Save"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="ID of the article to save",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article saved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="article saved successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function save(Article $article)
    {
        $data = $this->saveService->saveArticle($article);

        return ResponseBuilder::success(
            $data,
            'article saved successfully'
        );
    }

    /** @OA\Post(
     *     path="/api/unsave/{article}",
     *     summary="Unsave an article",
     *     description="Remove an article from the authenticated user's saved list",
     *     operationId="unsaveArticle",
     *     tags={"Save"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         description="ID of the article to unsave",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article unsaved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="article unsaved successfully")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function unsave(Article $article)
    {
        $data = $this->saveService->unsaveArticle($article);

        return ResponseBuilder::success(
            $data,
            'article unsaved successfully'
        );
    }
}
