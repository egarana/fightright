<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemberMembership>
 */
class MemberMembershipFactory extends Factory
{
    protected $model = MemberMembership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $membership = Membership::factory()->create();
        $startedAt = now();
        $expiredAt = $startedAt->copy()->addDays($membership->duration_days);

        return [
            'member_id' => Member::factory(),
            'membership_id' => $membership->id,
            'snapshot_membership_name' => $membership->name,
            'snapshot_max_attendance_qty' => $membership->max_attendance_qty,
            'snapshot_duration_days' => $membership->duration_days,
            'snapshot_price' => $membership->price,
            'started_at' => $startedAt,
            'expired_at' => $expiredAt,
            'status' => 'active',
        ];
    }

    /**
     * Configure with specific membership.
     */
    public function forMembership(Membership $membership): static
    {
        $startedAt = now();
        $expiredAt = $startedAt->copy()->addDays($membership->duration_days);

        return $this->state(fn(array $attributes) => [
            'membership_id' => $membership->id,
            'snapshot_membership_name' => $membership->name,
            'snapshot_max_attendance_qty' => $membership->max_attendance_qty,
            'snapshot_duration_days' => $membership->duration_days,
            'snapshot_price' => $membership->price,
            'started_at' => $startedAt,
            'expired_at' => $expiredAt,
        ]);
    }

    /**
     * Indicate that the membership is expired.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'started_at' => now()->subDays(60),
            'expired_at' => now()->subDays(30),
            'status' => 'expired',
        ]);
    }

    /**
     * Indicate that the membership is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
