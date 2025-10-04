<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Category\Http\Requests\CategoryStoreRequest;
use Modules\Category\Http\Requests\CategoryUpdateRequest;
use Modules\Category\Models\Category;
use Modules\Category\Transformers\CategoryResource;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Get list of categories",
     *     tags={"Categories"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category list retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category list retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="News"),
     *                     @OA\Property(property="slug", type="string", example="News"),
     *                     @OA\Property(property="description", type="string", example="Description here"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", example="2024-01-01 10:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2024-01-02 15:30:00")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $this->authorize('viewAny', Category::class);
        $categories = Category::latest()->paginate(10);

        return ResponseBuilder::success(
            CategoryResource::collection($categories),
            'Category list retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="News"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="News"),
     *                 @OA\Property(property="slug", type="string", example="News"),
     *                 @OA\Property(property="description", type="string", example="Description here"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01 10:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-01-01 10:00:00")
     *             )
     *         )
     *     )
     * )
     */
    public function store(CategoryStoreRequest $request)
    {
        $this->authorize('create', new Category());
        $data = $request->validated();
        $category = Category::create($data);

        return ResponseBuilder::success(
            new CategoryResource($category),
            'Category created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get category details",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category details fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category details fetched successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="News"),
     *                 @OA\Property(property="slug", type="string", example="News"),
     *                 @OA\Property(property="description", type="string", example="Description here"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01 10:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-01-02 10:00:00")
     *             )
     *         )
     *     )
     * )
     */
    public function show(Category $category)
    {
        $this->authorize('view', Category::class);
        return ResponseBuilder::success(
            new CategoryResource($category),
            'Category details fetched successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="name", type="string", example="Updated name"),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated name"),
     *                 @OA\Property(property="slug", type="string", example="updated-name"),
     *                 @OA\Property(property="description", type="string", example="Updated description"),
     *                 @OA\Property(property="is_active", type="boolean", example=false),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01 10:00:00"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-01-05 15:00:00")
     *             )
     *         )
     *     )
     * )
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        $data = $request->validated();
        $category->update($data);

        return ResponseBuilder::success(
            new CategoryResource($category),
            'Category updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Failed to delete category",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete category.")
     *         )
     *     )
     * )
     */
    public function destroy(Category $category)
    {
        $this->authorize('destroy', $category);
        $category->delete();
        return ResponseBuilder::success(null, 'Category deleted successfully.');
    }
}
