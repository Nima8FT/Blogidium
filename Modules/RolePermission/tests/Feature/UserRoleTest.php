<?php

namespace Modules\RolePermission\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_user_role() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.user.role.add', ['user' => $user]), ['roles' => [$role->id]]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'username',
                'email',
                'roles'
            ]
        ]);
    }

    public function test_can_not_add_user_role() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.user.role.add', ['user' => $user]), ['roles' => $role->id]);

        $response->assertStatus(422);
    }

    public function test_can_remove_user_role() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.user.role.remove', ['user' => $user]), ['roles' => [$role->id]]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'username',
                'email',
                'roles'
            ]
        ]);
    }

    public function test_can_not_remove_user_role() {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'role test'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson(route('api.user.role.remove', ['user' => $user]), ['roles' => $role->id]);

        $response->assertStatus(422);
    }
}
