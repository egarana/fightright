<?php

namespace Tests\Feature;

use App\Mail\MemberIdCard;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MemberIdCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_send_id_card_email()
    {
        Mail::fake();
        \Spatie\Permission\Models\Permission::create(['name' => 'edit_members', 'guard_name' => 'web']);

        $user = User::factory()->create();
        $user->givePermissionTo('edit_members');

        $member = Member::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('members.send-id-card', $member));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(MemberIdCard::class, function ($mail) use ($member) {
            return $mail->hasTo($member->email) &&
                $mail->member->id === $member->id;
        });
    }
}
