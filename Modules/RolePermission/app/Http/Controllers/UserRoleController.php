<?php

namespace Modules\RolePermission\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Modules\Auth\Services\ResponseBuilder;
use Modules\Auth\Transformers\UserResource;
use Modules\RolePermission\Http\Requests\UserRoleRequest;
use Modules\RolePermission\Models\UserRole;

class UserRoleController extends Controller
{
    use AuthorizesRequests;

    /**
     * @OA\Post(
     *     path="/api/user/{user}/addrole",
     *     summary="Add roles to a user",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"roles"},
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="List of role IDs to add"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Roles added successfully"
     *     )
     * )
     */
    public function addRole(UserRoleRequest $request, User $user)
    {
        $this->authorize('addRole', UserRole::class);
        $data = $request->validated();
        $user->assignRole($data['roles']);

        return ResponseBuilder::success(
            new UserResource($user),
            'Role added successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/user/{user}/removerole",
     *     summary="Remove roles from a user",
     *     tags={"Roles & Permission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="ID of the user"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"roles"},
     *             @OA\Property(
     *                 property="roles",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="List of role IDs to remove"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Roles removed successfully"
     *     )
     * )
     */
    public function removeRole(UserRoleRequest $request, User $user)
    {
        $this->authorize('removeRole', UserRole::class);
        $data = $request->validated();
        $user->removeRole($data['roles']);

        return ResponseBuilder::success(
            new UserResource($user),
            'Role removed successfully'
        );
    }
}
