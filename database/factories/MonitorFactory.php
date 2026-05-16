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
            'url'            => 'https://' . $this->faker->unique()->domainName(),
            'check_interval' => 5,
            'threshold'      => 3,
            'status'         => 'pending',
            'last_checked_at' => null,
            'consecutive_failures' => 0,
        ];
    }
}
