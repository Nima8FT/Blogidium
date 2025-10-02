<?php

namespace Modules\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Services\ResponseBuilder;
use Modules\RolePermission\Http\Requests\RolePermissionRequest;
use Modules\RolePermission\Transformers\RoleResource;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/role/{role}/addpermissions",
     *     summary="Add permissions to a role",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="ID of the role"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"permissions"},
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="List of permission IDs to add"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions added successfully"
     *     )
     * )
     */
    public function addPermissions(RolePermissionRequest $request, Role $role)
    {
        $data = $request->validated();
        $role->syncPermissions($data['permissions']);

        return ResponseBuilder::success(
            new RoleResource($role),
            'Permissions added successfully'
        );
    }

    /**
     * @OA\Post (
     *     path="/api/role/{role}/deletepermissions",
     *     summary="Remove permissions from a role",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="ID of the role"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"permissions"},
     *             @OA\Property(
     *                 property="permissions",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="List of permission IDs to remove"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions removed successfully"
     *     )
     * )
     */
    public function removePermissions(RolePermissionRequest $request, Role $role)
    {
        $data = $request->validated();
        foreach ($data['permissions'] as $permission) {
            $role->revokePermissionTo($permission);
        }

        return ResponseBuilder::success(
            new RoleResource($role),
            'Permissions removed successfully'
        );
    }
}
