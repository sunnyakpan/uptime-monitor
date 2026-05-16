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
        return [
            'status_code'      => 200,
            'response_time_ms' => $this->faker->numberBetween(50, 2000),
            'is_up'            => true,
            'checked_at'       => now(),
        ];
    }
}
