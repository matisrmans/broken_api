<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_posts()
    {
        $user = User::factory()->create();
        Post::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_can_store_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'Test Title',
            'body' => 'Test Body'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Title',
            'user_id' => $user->id
        ]);
    }

    public function test_can_update_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Title',
            'body' => 'Updated Body'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title'
        ]);
    }

    public function test_cannot_update_others_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);
        $this->actingAs($user);

        $response = $this->putJson("/api/posts/{$post->id}", [
            'title' => 'Updated Title',
            'body' => 'Updated Body'
        ]);

        $response->assertStatus(403);
    }

    public function test_can_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_cannot_delete_others_post()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $otherUser->id]);
        $this->actingAs($user);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);
    }
}
