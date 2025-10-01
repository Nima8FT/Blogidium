<?php

namespace Modules\Search\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Modules\Article\Models\Article as ArticleModel;
use Modules\Article\Transformers\ArticleResource;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Search\Http\Requests\SearchRequest;

class SearchController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/search",
     *     summary="Search articles",
     *     description="Search for articles by keyword",
     *     operationId="searchArticles",
     *     tags={"Search"},
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search keyword",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Search results retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Example Article"),
     *                     @OA\Property(property="content", type="string", example="Article content here")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Search Article retrieved successfully.")
     *         )
     *     )
     * )
     */
    public function search(SearchRequest $request)
    {
        $search = $request->validated();

        //        search base on milisearch
        //        for use this search uncomment searchable in model Modules\Article\Models\Article
        //        and comment indexable in model App\Models\Article
        //        and uncomment the code below is here
        $articles = ArticleModel::search($search['search'])->get();

        //        search base on elasticsearch
        //        for use this search comment searchable in model Modules\Article\Models\Article
        //        and uncomment indexable in model App\Models\Article
        //        and uncomment the code below is here
        //        default we use elasticsearch
        //        $articles = Article::search($search['search']);

        return ResponseBuilder::success(
            ArticleResource::collection($articles),
            'Search Article retrieved successfully.'
        );
    }
}
