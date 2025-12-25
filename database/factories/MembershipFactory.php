<?php

namespace Database\Factories;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Membership>
 */
class MembershipFactory extends Factory
{
    protected $model = Membership::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Bronze', 'Silver', 'Gold', 'Platinum']) . ' ' . fake()->word(),
            'description' => fake()->sentence(),
            'max_attendance_qty' => fake()->randomElement([8, 12, 16, 20, null]),
            'duration_days' => fake()->randomElement([7, 14, 30, 60, 90]),
            'price' => fake()->randomFloat(2, 100000, 1000000),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the membership is unlimited.
     */
    public function unlimited(): static
    {
        return $this->state(fn(array $attributes) => [
            'max_attendance_qty' => null,
        ]);
    }

    /**
     * Indicate that the membership is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
