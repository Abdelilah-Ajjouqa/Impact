<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register with valid credentials', function () {
    $response = $this->post('/register', [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'username' => 'janedoe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/login');
    $this->assertDatabaseHas('users', [
        'username' => 'janedoe',
        'email' => 'jane@example.com',
    ]);
});

test('registration fails when email or username is already taken', function () {
    User::factory()->create([
        'username' => 'takenuser',
        'email' => 'taken@example.com',
    ]);

    $response = $this->post('/register', [
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'username' => 'takenuser',
        'email' => 'taken@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['username', 'email']);
});

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'testuser@example.com',
        'password' => 'secret123',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'testuser@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'testuser@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
