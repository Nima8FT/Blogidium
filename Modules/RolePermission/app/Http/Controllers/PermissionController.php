<?php

namespace Modules\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Services\ResponseBuilder;
use Modules\RolePermission\Http\Requests\StorePermissionRequest;
use Modules\RolePermission\Http\Requests\UpdatePermissionRequest;
use Modules\RolePermission\Transformers\PermissionResource;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/permissions",
     *     summary="List all permissions",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Permissions retrieved successfully."
     *     )
     * )
     */
    public function index()
    {
        $permissions = Permission::latest()->paginate(10);

        return ResponseBuilder::success(
            PermissionResource::collection($permissions),
            'Permissions retrieved successfully.'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/permissions",
     *     summary="Create a new permission",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="edit-post",
     *                 description="Name of the permission"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission created successfully."
     *     )
     * )
     */
    public function store(StorePermissionRequest $request)
    {
        $data = $request->validated();
        $permissions = Permission::create($data);

        return ResponseBuilder::success(
            new PermissionResource($permissions),
            'Permission created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/permissions/{permission}",
     *     summary="Get details of a permission",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="permission",
     *         in="path",
     *         required=true,
     *         description="ID of the permission"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission details retrieved successfully."
     *     )
     * )
     */
    public function show(Permission $permission)
    {
        return ResponseBuilder::success(
            new PermissionResource($permission),
            'Permission details retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/permissions/{permission}",
     *     summary="Update a permission",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="permission",
     *         in="path",
     *         required=true,
     *         description="ID of the permission to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="edit-post",
     *                 description="New name of the permission"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission updated successfully."
     *     )
     * )
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $data = $request->validated();
        $permission->update($data);

        return ResponseBuilder::success(
            new PermissionResource($permission),
            'Permission updated successfully.'
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/permissions/{permission}",
     *     summary="Delete a permission",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="permission",
     *         in="path",
     *         required=true,
     *         description="ID of the permission to delete"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permission deleted successfully."
     *     )
     * )
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return ResponseBuilder::success(
            null,
            'Permission deleted successfully.'
        );
    }
}
