<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('guests cannot access users', function () {
    auth()->logout();
    $this->get(route('users.index'))->assertRedirect(route('login'));
});

test('authenticated users can view users list', function () {
    User::factory()->count(3)->create();

    $this->get(route('users.index'))
        ->assertStatus(200);
});

test('can create a user', function () {
    Role::create(['name' => 'staff']);

    $userData = [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'staff',
    ];

    $this->post(route('users.store'), $userData)
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'testuser@example.com',
    ]);

    $newUser = User::where('email', 'testuser@example.com')->first();
    expect($newUser->hasRole('staff'))->toBeTrue();
});

test('cannot create user with duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $userData = [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $this->post(route('users.store'), $userData)
        ->assertSessionHasErrors('email');
});

test('can update a user', function () {
    $user = User::factory()->create();

    $this->put(route('users.update', $user), [
        'name' => 'Updated Name',
        'email' => $user->email,
    ])->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
    ]);
});

test('can update user with new password', function () {
    $user = User::factory()->create();
    $oldPassword = $user->password;

    $this->put(route('users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertRedirect(route('users.index'));

    $user->refresh();
    expect($user->password)->not->toBe($oldPassword);
});

test('can delete a user', function () {
    $user = User::factory()->create();

    $this->delete(route('users.destroy', $user))
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});
