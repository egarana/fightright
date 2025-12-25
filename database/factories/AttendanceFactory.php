<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\MemberMembership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInAt = fake()->dateTimeBetween('-30 days', 'now');
        $checkOutAt = fake()->boolean(70)
            ? (clone $checkInAt)->modify('+' . fake()->numberBetween(30, 180) . ' minutes')
            : null;

        return [
            'member_membership_id' => MemberMembership::factory(),
            'snapshot_member_name' => fake()->name(),
            'snapshot_membership_name' => fake()->word() . ' Membership',
            'snapshot_remaining_before' => fake()->randomElement([1, 5, 10, 15, null]),
            'check_in_at' => $checkInAt,
            'check_out_at' => $checkOutAt,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Configure with specific member membership.
     */
    public function forMemberMembership(MemberMembership $mm): static
    {
        return $this->state(fn(array $attributes) => [
            'member_membership_id' => $mm->id,
            'snapshot_member_name' => $mm->member->name,
            'snapshot_membership_name' => $mm->snapshot_membership_name,
            'snapshot_remaining_before' => $mm->remaining_qty,
        ]);
    }

    /**
     * Indicate that the attendance is still checked-in (no check-out).
     */
    public function checkedIn(): static
    {
        return $this->state(fn(array $attributes) => [
            'check_in_at' => now(),
            'check_out_at' => null,
        ]);
    }
}
