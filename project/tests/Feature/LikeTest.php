<?php

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
});

test('guest cannot like a post and is redirected or unauthorized', function () {
    $post = Post::factory()->create(['status' => Post::STATUS_PUBLIC]);

    $response = $this->postJson("/posts/{$post->id}/like");

    $response->assertStatus(401);
});

test('authenticated user can like a post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['status' => Post::STATUS_PUBLIC]);

    $response = $this->actingAs($user)
        ->postJson("/posts/{$post->id}/like");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'liked' => true,
            'likes_count' => 1,
        ]);

    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    $this->assertTrue($post->isLikedBy($user));
});

test('authenticated user can unlike an already liked post', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create(['status' => Post::STATUS_PUBLIC]);
    Like::create(['user_id' => $user->id, 'post_id' => $post->id]);

    $response = $this->actingAs($user)
        ->postJson("/posts/{$post->id}/like");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'liked' => false,
            'likes_count' => 0,
        ]);

    $this->assertDatabaseMissing('likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    $this->assertFalse($post->isLikedBy($user));
});

test('multiple users liking a post updates the like counter accurately', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $post = Post::factory()->create(['status' => Post::STATUS_PUBLIC]);

    $this->actingAs($user1)->postJson("/posts/{$post->id}/like")
        ->assertJson(['liked' => true, 'likes_count' => 1]);

    $this->actingAs($user2)->postJson("/posts/{$post->id}/like")
        ->assertJson(['liked' => true, 'likes_count' => 2]);

    $this->actingAs($user1)->postJson("/posts/{$post->id}/like")
        ->assertJson(['liked' => false, 'likes_count' => 1]);
});

