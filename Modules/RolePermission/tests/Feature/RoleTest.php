<?php

namespace Modules\RolePermission\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_roles()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Role::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get(route('api.roles.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'permissions',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_it_can_not_list_roles()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        Role::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer free-token')
            ->get(route('api.roles.index'));

        $response->assertStatus(401);
    }

    public function test_it_can_create_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.roles.store'), ['name' => 'test']);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_not_create_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.roles.store'), []);

        $response->assertStatus(422);
    }

    public function test_it_can_show_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $role = Role::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(route('api.roles.show', ['role' => $role->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_not_show_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $role = Role::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(route('api.roles.show', ['role' => 'fake']));

        $response->assertStatus(500);
    }

    public function test_it_can_update_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(route('api.roles.update', ['role' => $role->id]), ['name' => 'test1']);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_can_not_update_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson(route('api.roles.update', ['role' => 'fake']), ['name' => 'test1']);

        $response->assertStatus(500);
    }

    public function test_it_can_delete_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $role = Role::create([
            'name' => 'test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson(route('api.roles.destroy', ['role' => $role->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
    }

    public function test_it_can_not_delete_role()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson(route('api.roles.destroy', ['role' => 'fake']));

        $response->assertStatus(500);
    }
}
