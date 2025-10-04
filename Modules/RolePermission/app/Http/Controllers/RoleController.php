<?php

namespace Modules\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Modules\Auth\Services\ResponseBuilder;
use Modules\RolePermission\Http\Requests\StoreRoleRequest;
use Modules\RolePermission\Http\Requests\UpdateRoleRequest;
use Modules\RolePermission\Transformers\RoleResource;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    use AuthorizesRequests;
    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="List all roles",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Roles retrieved successfully."
     *     )
     * )
     */
    public function index()
    {
        $this->authorize('viewAny', Role::class);
        $roles = Role::latest()->paginate(10);

        return ResponseBuilder::success(
            RoleResource::collection($roles),
            'Roles retrieved successfully.'
        );
    }


    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Create a new role",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="admin",
     *                 description="Name of the role"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role created successfully."
     *     )
     * )
     */
    public function store(StoreRoleRequest $request)
    {
        $this->authorize('create', Role::class);
        $data = $request->validated();
        $role = Role::create($data);

        return ResponseBuilder::success(
            new RoleResource($role),
            'Role created successfully.'
        );
    }

    /**
     * @OA\Get(
     *     path="/api/roles/{role}",
     *     summary="Get details of a role",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="ID of the role"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details retrieved successfully."
     *     )
     * )
     */
    public function show(Role $role)
    {
        $this->authorize('view', Role::class);
        return ResponseBuilder::success(
            new RoleResource($role),
            'Role details retrieved successfully.'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/roles/{role}",
     *     summary="Update a role",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="ID of the role to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="admin",
     *                 description="New name of the role"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully."
     *     )
     * )
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorize('update', Role::class);
        $data = $request->validated();
        $role->update($data);

        return ResponseBuilder::success(
            new RoleResource($role),
            'Role updated successfully.'
        );
    }


    /**
     * @OA\Delete(
     *     path="/api/roles/{role}",
     *     summary="Delete a role",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         required=true,
     *         description="ID of the role to delete"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role deleted successfully."
     *     )
     * )
     */
    public function destroy(Role $role)
    {
        $this->authorize('destroy', Role::class);
        $role->delete();

        return ResponseBuilder::success(
            null,
            'Role deleted successfully.'
        );
    }
}
