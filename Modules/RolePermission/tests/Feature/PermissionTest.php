<?php

namespace Modules\RolePermission\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Permission::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get(route('api.permissions.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'roles',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_it_can_not_list_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Permission::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer free-token')
            ->get(route('api.permissions.index'));

        $response->assertStatus(401);
    }

    public function test_it_can_create_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.permissions.store'), ['name' => 'test']);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'roles',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_not_create_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.permissions.store'), []);

        $response->assertStatus(422);
    }

    public function test_it_can_show_permission()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $permission = Permission::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(route('api.permissions.show', ['permission' => $permission]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'roles',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_not_show_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        Permission::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(route('api.permissions.show', ['permission' => 'fake']));

        $response->assertStatus(500);
    }

    public function test_it_can_update_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $permission = Permission::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(route('api.permissions.update', ['permission' => $permission->id]), ['name' => 'test1']);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'roles',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_not_update_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(route('api.permissions.update', ['permission' => 'fake']), ['name' => 'test1']);

        $response->assertStatus(500);
    }

    public function test_it_can_delete_permission()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $permission = Permission::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson(route('api.permissions.destroy', ['permission' => $permission->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
    }

    public function test_it_can_not_delete_permissions()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson(route('api.permissions.destroy', ['permission' => 'fake']));

        $response->assertStatus(500);
    }
}
