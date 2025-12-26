<?php

use App\Exceptions\AlreadyCheckedInException;
use App\Exceptions\MembershipExpiredException;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\Membership;
use App\Models\User;
use App\Services\AttendanceService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('guests cannot access attendances', function () {
    auth()->logout();
    $this->get(route('attendances.index'))->assertRedirect(route('login'));
});

test('can check-in with valid membership', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->create([
        'max_attendance_qty' => 10,
        'duration_days' => 30,
    ]);

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->create();

    $this->post(route('attendances.check-in'), [
        'member_membership_id' => $memberMembership->id,
    ])->assertRedirect(route('attendances.today'));

    $this->assertDatabaseHas('attendances', [
        'member_membership_id' => $memberMembership->id,
        'snapshot_member_name' => $member->name,
        'snapshot_remaining_before' => 10,
    ]);
});

test('cannot check-in with expired membership', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->create([
        'max_attendance_qty' => 10,
        'duration_days' => 30,
    ]);

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->expired()
        ->create();

    $this->post(route('attendances.check-in'), [
        'member_membership_id' => $memberMembership->id,
    ])->assertSessionHasErrors('member_membership_id');

    $this->assertDatabaseCount('attendances', 0);
});

test('cannot check-in with exhausted quota', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->create([
        'max_attendance_qty' => 2,
        'duration_days' => 30,
    ]);

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->create();

    // Create 2 attendances to exhaust quota
    Attendance::factory()->count(2)->create([
        'member_membership_id' => $memberMembership->id,
    ]);

    $this->post(route('attendances.check-in'), [
        'member_membership_id' => $memberMembership->id,
    ])->assertSessionHasErrors('member_membership_id');
});

test('can check-in with unlimited membership', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->unlimited()->create([
        'duration_days' => 30,
    ]);

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->create();

    // Create many attendances - should still work because unlimited
    // Make sure they're all checked out
    Attendance::factory()->count(50)->create([
        'member_membership_id' => $memberMembership->id,
        'check_out_at' => now(),
    ]);

    $this->post(route('attendances.check-in'), [
        'member_membership_id' => $memberMembership->id,
    ])->assertRedirect(route('attendances.today'));
});

test('cannot double check-in', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->create([
        'max_attendance_qty' => 10,
        'duration_days' => 30,
    ]);

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->create();

    // First check-in (still active - no check-out)
    Attendance::factory()->checkedIn()->create([
        'member_membership_id' => $memberMembership->id,
    ]);

    $this->post(route('attendances.check-in'), [
        'member_membership_id' => $memberMembership->id,
    ])->assertSessionHasErrors('member_membership_id');
});

test('can check-out', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->create();

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->create();

    $attendance = Attendance::factory()->checkedIn()->create([
        'member_membership_id' => $memberMembership->id,
    ]);

    $this->post(route('attendances.check-out', $attendance))
        ->assertRedirect(route('attendances.today'));

    $attendance->refresh();
    expect($attendance->check_out_at)->not->toBeNull();
});

test('remaining qty is calculated correctly', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->create([
        'max_attendance_qty' => 10,
        'duration_days' => 30,
    ]);

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->create();

    expect($memberMembership->remaining_qty)->toBe(10);
    expect($memberMembership->used_qty)->toBe(0);

    // Add 3 attendances
    Attendance::factory()->count(3)->create([
        'member_membership_id' => $memberMembership->id,
    ]);

    $memberMembership->refresh();
    expect($memberMembership->remaining_qty)->toBe(7);
    expect($memberMembership->used_qty)->toBe(3);
});

test('unlimited membership returns null for remaining qty', function () {
    $member = Member::factory()->create();
    $membership = Membership::factory()->unlimited()->create();

    $memberMembership = MemberMembership::factory()
        ->for($member)
        ->forMembership($membership)
        ->create();

    expect($memberMembership->remaining_qty)->toBeNull();
    expect($memberMembership->canCheckIn())->toBeTrue();
});
