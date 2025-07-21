<?php

namespace Modules\Article\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Article\Http\Requests\ArticleStoreRequest;
use Modules\Article\Http\Requests\ArticleUpdateRequest;
use Modules\Article\Models\Article;
use Modules\Article\Services\Contracts\ArticleServiceInterface;
use Modules\Article\Transformers\ArticleResource;
use Modules\Auth\Services\Contracts\AuthServiceInterface;
use Modules\Auth\Services\ResponseBuilder;

class ArticleController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService,
        private ArticleServiceInterface $articleService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Get a list of articles",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Article list retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="My First Article"),
     *                     @OA\Property(property="slug", type="string", example="my-first-article"),
     *                     @OA\Property(property="content", type="string", example="Full content..."),
     *                     @OA\Property(property="author", type="object",
     *                         @OA\Property(property="author_name", type="string", example="johndoe")
     *                     ),
     *                     @OA\Property(property="category", type="object",
     *                         @OA\Property(property="category_name", type="string", example="Tech"),
     *                         @OA\Property(property="category_slug", type="string", example="tech")
     *                     ),
     *                     @OA\Property(property="is_published", type="boolean", example=true),
     *                     @OA\Property(property="published_at", type="string", example="3 days ago"),
     *                     @OA\Property(property="created_at", type="string", example="2 weeks ago"),
     *                     @OA\Property(property="updated_at", type="string", example="1 week ago")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $articles = Article::latest()->paginate(10);

        return ResponseBuilder::success(
            ArticleResource::collection($articles),
            'Article list retrieved successfully.'
        );
    }


    /**
     * @OA\Post(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Create a new article",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "category_id"},
     *             @OA\Property(property="title", type="string", example="New Article"),
     *             @OA\Property(property="content", type="string", example="Article content here."),
     *             @OA\Property(property="category_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Article created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="New Article"),
     *                 @OA\Property(property="slug", type="string", example="new-article"),
     *                 @OA\Property(property="content", type="string", example="Content..."),
     *                 @OA\Property(property="author", type="object",
     *                     @OA\Property(property="author_name", type="string", example="johndoe")
     *                 ),
     *                 @OA\Property(property="category", type="object",
     *                     @OA\Property(property="category_name", type="string", example="Tech"),
     *                     @OA\Property(property="category_slug", type="string", example="tech")
     *                 ),
     *                 @OA\Property(property="is_published", type="boolean", example=true),
     *                 @OA\Property(property="published_at", type="string", example="just now"),
     *                 @OA\Property(property="created_at", type="string", example="just now"),
     *                 @OA\Property(property="updated_at", type="string", example="just now")
     *             )
     *         )
     *     )
     * )
     */
    public function store(ArticleStoreRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->getUser();
        $article = $this->articleService->create($data, $user);

        return ResponseBuilder::success(
            new ArticleResource($article),
            'Article created successfully.'
        );
    }


    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Get article details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Article ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Article details fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="slug", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="author", type="object",
     *                     @OA\Property(property="author_name", type="string")
     *                 ),
     *                 @OA\Property(property="category", type="object",
     *                     @OA\Property(property="category_name", type="string"),
     *                     @OA\Property(property="category_slug", type="string")
     *                 ),
     *                 @OA\Property(property="created_at", type="string"),
     *                 @OA\Property(property="updated_at", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function show(Article $article)
    {
        return ResponseBuilder::success(
            new ArticleResource($article),
            'Article details fetched successfully.'
        );
    }


    /**
     * @OA\Put(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Update an existing article",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated title"),
     *             @OA\Property(property="content", type="string", example="Updated content"),
     *             @OA\Property(property="category_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Article updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="updated_at", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function update(ArticleUpdateRequest $request, Article $article)
    {
        $data = $request->validated();
        $user = $this->authService->getUser();
        $is_update = $this->articleService->update($article, $data, $user);
        if ($is_update) {
            return ResponseBuilder::success(
                new ArticleResource($article),
                'Article updated successfully.'
            );
        }

        return ResponseBuilder::error(
            'Article not for you.'
        );
    }



    /**
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Delete an article",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the article to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Article deleted successfully.")
     *         )
     *     )
     * )
     */
    public function destroy(Article $article)
    {
        try {
            $user = $this->authService->getUser();
            $is_deleted = $this->articleService->delete($article, $user);
            if ($is_deleted) {
                return ResponseBuilder::success(null, 'Article deleted successfully.');
            }

            return ResponseBuilder::error('Article not for you.');
        } catch (\Exception $e) {
            return ResponseBuilder::error($e->getMessage());
        }
    }
}
