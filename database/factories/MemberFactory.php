<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => [
                'country' => [
                    'country' => 'ID',
                    'countryName' => 'Indonesia',
                    'code' => '+62',
                ],
                'number' => fake()->numerify('8##########'),
            ],
            'address' => fake()->address(),
        ];
    }
}
