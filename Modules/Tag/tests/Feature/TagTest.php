<?php

namespace Modules\Tag\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tag\Models\Tag;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_it_can_list_tags(): void
    {
        Tag::factory()->create();

        $response = $this->getJson(route('api.tags.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_it_can_create_tag(): void
    {
        $response = $this->postJson(route('api.tags.store'), [
            'name' => 'test',
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

    public function test_it_cannot_create_tag_without_name(): void
    {
        $response = $this->postJson(route('api.tags.store'), [
            'slug' => 'test',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_can_show_tag(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->getJson(route('api.tags.show', $tag->id));

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

    public function test_it_cannot_show_tag_with_invalid_id(): void
    {
        $response = $this->getJson(route('api.tags.show', 999));
        $response->assertStatus(404);
    }

    public function test_it_can_update_tag(): void
    {
        $tag = Tag::factory()->create();
        $response = $this->putJson(route('api.tags.update', $tag->id), [
            'name' => 'test',
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

    public function test_it_cannot_update_tag_without_unique_name(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'test',
        ]);

        $response = $this->putJson(route('api.tags.update', $tag->id), [
            'name' => 'test',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_can_delete_tag(): void
    {
        $tag = Tag::factory()->create();
        $response = $this->deleteJson(route('api.tags.destroy', $tag->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
    }

    public function test_it_cannot_delete_tag_with_invalid_id(): void
    {
        $response = $this->deleteJson(route('api.tags.destroy', 999));
        $response->assertStatus(404);
    }
}
