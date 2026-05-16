<?php

namespace Database\Factories;

use App\Models\MonitorCheck;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonitorCheck>
 */
class MonitorCheckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isUp = $this->faker->boolean(80); // 80% chance of being up

        return [
            'status_code'      => $isUp
                ? $this->faker->randomElement([200, 201, 301, 302])
                : $this->faker->randomElement([0, 500, 503, 404]),
            'response_time_ms' => $isUp
                ? $this->faker->numberBetween(50, 2000)
                : null,
            'is_up'            => $isUp,
            'checked_at'       => $this->faker->dateTimeThisMonth(),
        ];
    }
}
