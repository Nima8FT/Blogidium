<?php

namespace Modules\Category\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_categories()
    {
        Category::factory()->count(5)->create();

        $response = $this->getJson(route('api.category.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'is_active',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_it_can_create_a_category()
    {
        $response = $this->postJson(route('api.category.store'), [
            'name' => 'Test Category',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'slug',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_cannot_create_a_category_with_invalid_data()
    {
        $response = $this->postJson(route('api.category.store'), [
            'name' => 12345678,
        ]);

        $response->assertStatus(422);
    }

    public function test_it_can_show_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson(route('api.category.show', $category->id));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'is_active',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_returns_404_when_category_does_not_exist()
    {
        $response = $this->getJson(route('api.category.show', 999));
        $response->assertNotFound();
    }

    public function test_it_can_update_a_category()
    {
        $category = Category::factory()->create();
        $response = $this->putJson(route('api.category.update', $category->id), [
            'name' => 'Test Category',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'is_active',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_it_cannot_update_a_category_with_invalid_data()
    {
        $category = Category::factory()->create();
        $response = $this->putJson(route('api.category.update', $category->id), [
            'name' => 12345678,
        ]);
        $response->assertStatus(422);
    }

    public function test_it_can_delete_a_category()
    {
        $category = Category::factory()->create();
        $response = $this->deleteJson(route('api.category.destroy', $category->id));
        $response->assertStatus(200);
    }

    public function test_it_cannot_delete_a_category_with_invalid_data()
    {
        $category = Category::factory()->create();
        $response = $this->deleteJson(route('api.category.destroy', $category->id));
        $response->assertStatus(200);
        $response = $this->deleteJson(route('api.category.destroy', $category->id));
        $response->assertStatus(404);
    }
}
