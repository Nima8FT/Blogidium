<?php

namespace Modules\RolePermission\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_role_permission() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $permission = Permission::create([
            'name' => 'permission test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.role.permissions.add', ['role' => $role]), ['permissions' => [$permission->id]]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_not_add_role_permission() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $permission = Permission::create([
            'name' => 'permission test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.role.permissions.add', ['role' => $role]), ['permissions' => $permission->id]);

        $response->assertStatus(422);
    }

    public function test_can_remove_role_permission() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $permission = Permission::create([
            'name' => 'permission test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.role.permissions.remove', ['role' => $role]), ['permissions' => [$permission->id]]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    public function test_can_not_remove_role_permission() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $permission = Permission::create([
            'name' => 'permission test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.role.permissions.remove', ['role' => $role]), ['permissions' => $permission->id]);

        $response->assertStatus(422);
    }
}
