<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    // Create roles
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

    $this->superAdmin = User::factory()->create();
    $this->superAdmin->assignRole('super-admin');

    $this->owner = User::factory()->create();
    $this->owner->assignRole('owner');

    $this->manager = User::factory()->create();
    $this->manager->assignRole('manager');

    $this->staff = User::factory()->create();
    $this->staff->assignRole('staff');
});

test('super-admin can impersonate non-super-admin user', function () {
    $this->actingAs($this->superAdmin);

    $this->post(route('impersonate.start', $this->owner))
        ->assertRedirect(route('dashboard.index'));

    expect(auth()->id())->toBe($this->owner->id);
    expect(session('impersonator_id'))->toBe($this->superAdmin->id);
});

test('super-admin cannot impersonate another super-admin', function () {
    $anotherSuperAdmin = User::factory()->create();
    $anotherSuperAdmin->assignRole('super-admin');

    $this->actingAs($this->superAdmin);

    $this->post(route('impersonate.start', $anotherSuperAdmin))
        ->assertRedirect();

    expect(auth()->id())->toBe($this->superAdmin->id);
});

test('super-admin cannot impersonate themselves', function () {
    $this->actingAs($this->superAdmin);

    $this->post(route('impersonate.start', $this->superAdmin))
        ->assertRedirect();

    expect(auth()->id())->toBe($this->superAdmin->id);
});

test('non-super-admin cannot impersonate', function () {
    $this->actingAs($this->owner);

    $this->post(route('impersonate.start', $this->manager))
        ->assertStatus(404);
});

test('can stop impersonating', function () {
    // Simulate being impersonated
    $this->actingAs($this->owner)
        ->withSession(['impersonator_id' => $this->superAdmin->id]);

    $this->post(route('impersonate.stop'))
        ->assertRedirect(route('dashboard.index'));

    expect(auth()->id())->toBe($this->superAdmin->id);
});

test('impersonated user sees valid permissions', function () {
    // 1. Check super admin has manage_users
    $this->actingAs($this->superAdmin)
        ->get(route('dashboard.index'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('auth.can.manage_users', true)
        );

    // 2. Impersonate staff (simulate session)
    // Staff should NOT see 'manage_users' even if impersonated by super admin
    $this->actingAs($this->staff)
        ->withSession(['impersonator_id' => $this->superAdmin->id])
        ->get(route('dashboard.index'))
        ->assertInertia(
            fn(Assert $page) => $page
                ->where('auth.can.manage_users', false)
                ->where('impersonation.active', true)
                ->where('impersonation.impersonator.id', $this->superAdmin->id)
        );
});
