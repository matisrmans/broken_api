<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_comments_for_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        Comment::factory()->count(3)->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        $response = $this->getJson("/api/posts/{$post->id}/comments");

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_can_store_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->postJson("/api/posts/{$post->id}/comments", [
            'content' => 'Test Comment'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'content' => 'Test Comment',
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);
    }

    public function test_can_update_own_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);
        $this->actingAs($user);

        $response = $this->putJson("/api/posts/{$post->id}/comments/{$comment->id}", [
            'content' => 'Updated Comment'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated Comment'
        ]);
    }

    public function test_cannot_update_others_comment()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $otherUser->id
        ]);
        $this->actingAs($user);

        $response = $this->putJson("/api/posts/{$post->id}/comments/{$comment->id}", [
            'content' => 'Updated Comment'
        ]);

        $response->assertStatus(403);
    }
}
