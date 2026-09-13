<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can view posts feed', function () {
    $post = Post::factory()->create(['status' => Post::STATUS_PUBLIC]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee($post->title);
});

test('guest can view a public post', function () {
    $post = Post::factory()->create(['status' => Post::STATUS_PUBLIC]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertStatus(200);
    $response->assertSee($post->title);
});

test('guest cannot view a private post', function () {
    $post = Post::factory()->create(['status' => Post::STATUS_PRIVATE]);

    $response = $this->get("/posts/{$post->id}");

    $response->assertStatus(403);
});

test('author can view their own private post', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'status' => Post::STATUS_PRIVATE,
    ]);

    $response = $this->actingAs($author)->get("/posts/{$post->id}");

    $response->assertStatus(200);
    $response->assertSee($post->title);
});

test('non-author cannot edit another users post', function () {
    $author = User::factory()->create();
    $attacker = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'status' => Post::STATUS_PUBLIC,
    ]);

    $response = $this->actingAs($attacker)->get("/posts/{$post->id}/edit");

    $response->assertStatus(403);
});

test('non-author cannot update another users post', function () {
    $author = User::factory()->create();
    $attacker = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'status' => Post::STATUS_PUBLIC,
    ]);

    $response = $this->actingAs($attacker)->put("/posts/{$post->id}/update", [
        'title' => 'Hacked title',
        'status' => Post::STATUS_PUBLIC,
    ]);

    $response->assertStatus(403);
});

test('author can update their own post', function () {
    $author = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'status' => Post::STATUS_PUBLIC,
    ]);

    $response = $this->actingAs($author)->put("/posts/{$post->id}/update", [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => Post::STATUS_PUBLIC,
    ]);

    $response->assertRedirect(route('posts.show', $post->id));
    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Title',
    ]);
});

test('non-author cannot delete another users post', function () {
    $author = User::factory()->create();
    $attacker = User::factory()->create();
    $post = Post::factory()->create([
        'user_id' => $author->id,
        'status' => Post::STATUS_PUBLIC,
    ]);

    $response = $this->actingAs($attacker)->delete("/posts/{$post->id}");

    $response->assertStatus(403);
});
