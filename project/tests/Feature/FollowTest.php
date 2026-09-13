<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
});

test('authenticated user can follow another user', function () {
    $follower = User::factory()->create();
    $target = User::factory()->create();

    $response = $this->actingAs($follower)
        ->postJson("/users/{$target->id}/follow");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'following' => true,
            'followers_count' => 1,
        ]);

    $this->assertTrue($follower->isFollowing($target));
});

test('authenticated user can unfollow a followed user', function () {
    $follower = User::factory()->create();
    $target = User::factory()->create();
    $follower->following()->attach($target->id);

    $response = $this->actingAs($follower)
        ->postJson("/users/{$target->id}/follow");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'following' => false,
            'followers_count' => 0,
        ]);

    $this->assertFalse($follower->isFollowing($target));
});

test('user cannot follow themselves', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson("/users/{$user->id}/follow");

    $response->assertStatus(422)
        ->assertJson([
            'error' => 'You cannot follow yourself.',
        ]);
});

test('guest cannot follow a user', function () {
    $target = User::factory()->create();

    $response = $this->postJson("/users/{$target->id}/follow");

    $response->assertStatus(401);
});

