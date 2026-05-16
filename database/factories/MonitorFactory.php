<?php

namespace Database\Factories;

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Monitor>
 */
class MonitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url'                  => 'https://' . $this->faker->unique()->domainName(),
            'check_interval'       => $this->faker->randomElement([1, 5, 10, 15, 30]),
            'threshold'            => $this->faker->numberBetween(1, 5),
            'status'               => $this->faker->randomElement(['pending', 'up', 'down']),
            'last_checked_at'      => $this->faker->optional()->dateTimeThisMonth(),
            'consecutive_failures' => 0,
        ];
    }
}
