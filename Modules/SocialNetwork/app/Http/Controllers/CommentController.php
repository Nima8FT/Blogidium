<?php

namespace Modules\SocialNetwork\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Article\Models\Article;
use Modules\Auth\Services\ResponseBuilder;
use Modules\SocialNetwork\Http\Requests\CommentStoreRequest;
use Modules\SocialNetwork\Http\Requests\CommentUpdateRequest;
use Modules\SocialNetwork\Models\Comment;
use Modules\SocialNetwork\Services\Contracts\CommentServiceInterface;
use Modules\SocialNetwork\Transformers\CommentResource;

class CommentController extends Controller
{
    public function __construct(private CommentServiceInterface $commentService) {}

    /**
     * @OA\Get(
     *     path="/api/articles/{article}/comments",
     *     summary="Get list of comments for an article",
     *     description="Retrieve all comments related to a specific article. Requires authentication.",
     *     operationId="getArticleComments",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="List of comments retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string", example="Comments retrieved successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Article $article)
    {
        $comments = $article->comments()->latest()->get();

        return ResponseBuilder::success(
            CommentResource::collection($comments),
            'Comments retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/articles/{article}/comments",
     *     summary="Create a new comment for an article",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Comment data",
     *
     *         @OA\JsonContent(
     *             required={"content"},
     *
     *             @OA\Property(property="content", type="string", example="Nice article!"),
     *             @OA\Property(property="parent_id", type="integer", example=5)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Comment created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or parent comment invalid"
     *     )
     * )
     */
    public function store(CommentStoreRequest $request, Article $article)
    {
        $data = $request->validated();
        $comment = $this->commentService->create($article, $data);
        if ($comment) {
            return ResponseBuilder::success(
                new CommentResource($comment),
                'Your comment has been posted successfully.'
            );
        }

        return ResponseBuilder::error(
            'An error occurred while posting the comment.',
        );
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{article}/comments/{comment}",
     *     summary="Get a specific comment for an article",
     *     description="Retrieve details of a specific comment belonging to a specific article. Requires authentication.",
     *     operationId="getSpecificComment",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         description="ID of the comment",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Comment details retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Comment details retrieved successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found or not for this article"
     *     )
     * )
     */
    public function show(Article $article, Comment $comment)
    {
        if ($comment->article_id === $article->id) {
            return ResponseBuilder::success(
                new CommentResource($comment),
                'Comment details retrieved successfully.'
            );
        }

        return ResponseBuilder::error(
            'This comment does not belong to the selected article.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/articles/{article}/comments/{comment}",
     *     summary="Update a comment for an article",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         description="ID of the comment",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated comment data",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="content", type="string", example="Updated comment content."),
     *             @OA\Property(property="parent_id", type="integer", example=3)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Comment updated successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to update this comment"
     *     )
     * )
     */
    public function update(CommentUpdateRequest $request, Article $article, Comment $comment)
    {
        $data = $request->validated();
        $response = $this->commentService->update($article, $data, $comment);
        if ($response) {
            return ResponseBuilder::success(
                new CommentResource($comment),
                'Your comment has been updated successfully.'
            );
        }

        return ResponseBuilder::error('You are not authorized to update this comment or it does not belong to the selected article.');
    }

    /**
     * @OA\Delete(
     *     path="/api/articles/{article}/comments/{comment}",
     *     summary="Delete a comment for an article",
     *     tags={"Comments"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="article",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="comment",
     *         in="path",
     *         required=true,
     *         description="ID of the comment",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized to delete this comment"
     *     )
     * )
     */
    public function destroy(Article $article, Comment $comment)
    {
        $response = $this->commentService->delete($article, $comment);
        if ($response) {
            return ResponseBuilder::success(
                null,
                'Comment deleted successfully.'
            );
        }

        return ResponseBuilder::error(
            'An error occurred while deleting the comment.',
        );
    }
}
