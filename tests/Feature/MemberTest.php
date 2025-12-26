<?php

use App\Models\Member;
use App\Models\User;

beforeEach(function () {
    // Create owner role for testing (has edit/delete members permission)
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);

    $this->user = User::factory()->create();
    $this->user->assignRole('owner');
    $this->actingAs($this->user);
});

test('guests cannot access members', function () {
    auth()->logout();
    $this->get(route('members.index'))->assertRedirect(route('login'));
});

test('authenticated users can view members list', function () {
    Member::factory()->count(3)->create();

    $this->get(route('members.index'))
        ->assertStatus(200);
});

test('can create a member', function () {
    $phoneData = json_encode([
        'country' => [
            'country' => 'ID',
            'countryName' => 'Indonesia',
            'code' => '+62',
        ],
        'number' => '81234567890',
    ]);

    $memberData = [
        'name' => 'Test Member',
        'email' => 'test@example.com',
        'phone' => $phoneData,
        'address' => 'Test Address',
    ];

    $this->post(route('members.store'), $memberData)
        ->assertRedirect(route('members.index', ['sort' => '-created_at']));

    $this->assertDatabaseHas('members', [
        'name' => 'Test Member',
        'email' => 'test@example.com',
    ]);
});

test('cannot create member with duplicate email', function () {
    Member::factory()->create(['email' => 'existing@example.com']);

    $phoneData = json_encode([
        'country' => [
            'country' => 'ID',
            'countryName' => 'Indonesia',
            'code' => '+62',
        ],
        'number' => '81234567890',
    ]);

    $memberData = [
        'name' => 'Test Member',
        'email' => 'existing@example.com',
        'phone' => $phoneData,
    ];

    $this->post(route('members.store'), $memberData)
        ->assertSessionHasErrors('email');
});

test('can update a member', function () {
    $member = Member::factory()->create();

    $phoneData = json_encode([
        'country' => [
            'country' => 'ID',
            'countryName' => 'Indonesia',
            'code' => '+62',
        ],
        'number' => '81234567890',
    ]);

    $this->put(route('members.update', $member), [
        'name' => 'Updated Name',
        'email' => $member->email,
        'phone' => $phoneData,
        'address' => 'Updated Address',
    ])->assertRedirect(route('members.index', ['sort' => '-updated_at']));

    $this->assertDatabaseHas('members', [
        'id' => $member->id,
        'name' => 'Updated Name',
    ]);
});

test('can delete a member', function () {
    $member = Member::factory()->create();

    $this->delete(route('members.destroy', $member))
        ->assertRedirect(route('members.index'));

    $this->assertDatabaseMissing('members', [
        'id' => $member->id,
    ]);
});
