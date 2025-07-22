<?php

namespace Modules\Tag\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Tag\Http\Requests\TagStoreRequest;
use Modules\Tag\Http\Requests\TagUpdateRequest;
use Modules\Tag\Models\Tag;
use Modules\Tag\Transformers\TagResource;

class TagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tags",
     *     summary="Get list of tags",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Tag list retrieved successfully."
     *     )
     * )
     */
    public function index()
    {
        $tags = Tag::latest()->paginate(10);

        return ResponseBuilder::success(
            TagResource::collection($tags),
            'Tag list retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/tags",
     *     summary="Create a new tag",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Laravel")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag created successfully."
     *     )
     * )
     */
    public function store(TagStoreRequest $request)
    {
        $data = $request->validated();
        $tag = Tag::create($data);

        return ResponseBuilder::success(
            new TagResource($tag),
            'Tag created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/tags/{id}",
     *     summary="Get a single tag",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag retrieved successfully."
     *     )
     * )
     */
    public function show(Tag $tag)
    {
        return ResponseBuilder::success(
            new TagResource($tag),
            'Tag retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/tags/{id}",
     *     summary="Update a tag",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Updated Tag Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag updated successfully."
     *     )
     * )
     */
    public function update(TagUpdateRequest $request, Tag $tag)
    {
        $data = $request->validated();
        $tag->update($data);

        return ResponseBuilder::success(
            new TagResource($tag),
            'Tag updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/tags/{id}",
     *     summary="Delete a tag",
     *     tags={"Tags"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Tag ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tag deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Tag cannot be deleted at this moment."
     *     )
     * )
     */
    public function destroy(Tag $tag)
    {
        try {
            $tag->delete();

            return ResponseBuilder::success(
                null,
                'Tag deleted successfully.'
            );
        } catch (\Exception $e) {
            return ResponseBuilder::error('Tag cannot be deleted at this moment.');
        }
    }
}
