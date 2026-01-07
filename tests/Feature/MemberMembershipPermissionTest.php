<?php

use App\Models\Member;
use App\Models\Membership;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create roles
    Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);

    // Create common test data
    $this->membership = Membership::factory()->create([
        'name' => 'Test Membership',
        'duration_days' => 30,
        'max_attendance_qty' => 10,
        'price' => 100000,
    ]);

    $this->member = Member::factory()->create([
        'name' => 'Test Member',
    ]);
});

test('staff can add membership to member', function () {
    $staff = User::factory()->create();
    $staff->assignRole('staff');
    $this->actingAs($staff);

    $response = $this->post(route('members.memberships.store', $this->member), [
        'membership_id' => $this->membership->id,
        'started_at' => now()->format('Y-m-d'),
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('member_memberships', [
        'member_id' => $this->member->id,
        'snapshot_membership_name' => $this->membership->name,
    ]);
});

test('staff cannot delete membership from member', function () {
    $staff = User::factory()->create();
    $staff->assignRole('staff');

    // First add membership as manager
    $manager = User::factory()->create();
    $manager->assignRole('manager');
    $this->actingAs($manager);

    $this->post(route('members.memberships.store', $this->member), [
        'membership_id' => $this->membership->id,
        'started_at' => now()->format('Y-m-d'),
    ]);

    $memberMembership = $this->member->memberMemberships()->first();

    // Now try to delete as staff
    $this->actingAs($staff);

    $response = $this->delete(route('members.memberships.destroy', [
        'member' => $this->member,
        'memberMembership' => $memberMembership,
    ]));

    // Should get 404 (no permission)
    $response->assertNotFound();

    // Membership should still exist
    $this->assertDatabaseHas('member_memberships', [
        'id' => $memberMembership->id,
    ]);
});

test('manager can add and delete membership from member', function () {
    $manager = User::factory()->create();
    $manager->assignRole('manager');
    $this->actingAs($manager);

    // Add membership
    $this->post(route('members.memberships.store', $this->member), [
        'membership_id' => $this->membership->id,
        'started_at' => now()->format('Y-m-d'),
    ])->assertRedirect();

    $memberMembership = $this->member->memberMemberships()->first();

    // Delete membership
    $this->delete(route('members.memberships.destroy', [
        'member' => $this->member,
        'memberMembership' => $memberMembership,
    ]))->assertRedirect();

    $this->assertDatabaseMissing('member_memberships', [
        'id' => $memberMembership->id,
    ]);
});

test('owner can add and delete membership from member', function () {
    $owner = User::factory()->create();
    $owner->assignRole('owner');
    $this->actingAs($owner);

    // Add membership
    $this->post(route('members.memberships.store', $this->member), [
        'membership_id' => $this->membership->id,
        'started_at' => now()->format('Y-m-d'),
    ])->assertRedirect();

    $memberMembership = $this->member->memberMemberships()->first();

    // Delete membership
    $this->delete(route('members.memberships.destroy', [
        'member' => $this->member,
        'memberMembership' => $memberMembership,
    ]))->assertRedirect();

    $this->assertDatabaseMissing('member_memberships', [
        'id' => $memberMembership->id,
    ]);
});
