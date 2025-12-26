<?php

use App\Models\Membership;
use App\Models\User;

beforeEach(function () {
    // Create owner role for testing (has manage_memberships permission)
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

    $this->user = User::factory()->create();
    $this->user->assignRole('owner');
    $this->actingAs($this->user);
});

test('guests cannot access memberships', function () {
    auth()->logout();
    $this->get(route('memberships.index'))->assertRedirect(route('login'));
});

test('authenticated users can view memberships list', function () {
    Membership::factory()->count(3)->create();

    $this->get(route('memberships.index'))
        ->assertStatus(200);
});

test('can create a membership', function () {
    $membershipData = [
        'name' => 'Gold Membership',
        'description' => 'Premium access',
        'max_attendance_qty' => 20,
        'duration_days' => 30,
        'price' => 500000,
        'is_active' => true,
    ];

    $this->post(route('memberships.store'), $membershipData)
        ->assertRedirect(route('memberships.index', ['sort' => '-created_at']));

    $this->assertDatabaseHas('memberships', [
        'name' => 'Gold Membership',
        'max_attendance_qty' => 20,
    ]);
});

test('can create unlimited membership', function () {
    $membershipData = [
        'name' => 'Unlimited Membership',
        'max_attendance_qty' => null,
        'duration_days' => 30,
        'price' => 1000000,
    ];

    $this->post(route('memberships.store'), $membershipData)
        ->assertRedirect(route('memberships.index', ['sort' => '-created_at']));

    $this->assertDatabaseHas('memberships', [
        'name' => 'Unlimited Membership',
        'max_attendance_qty' => null,
    ]);
});

test('can update a membership', function () {
    $membership = Membership::factory()->create();

    $this->put(route('memberships.update', $membership), [
        'name' => 'Updated Membership',
        'duration_days' => 60,
        'price' => 750000,
    ])->assertRedirect(route('memberships.index', ['sort' => '-updated_at']));

    $this->assertDatabaseHas('memberships', [
        'id' => $membership->id,
        'name' => 'Updated Membership',
        'duration_days' => 60,
    ]);
});

test('can delete a membership', function () {
    $membership = Membership::factory()->create();

    $this->delete(route('memberships.destroy', $membership))
        ->assertRedirect(route('memberships.index'));

    $this->assertDatabaseMissing('memberships', [
        'id' => $membership->id,
    ]);
});
