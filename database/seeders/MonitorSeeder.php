<?php

namespace Database\Seeders;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use Illuminate\Database\Seeder;

class MonitorSeeder extends Seeder
{
    public function run(): void
    {
        // Create 5 monitors, each with 20 check history records
        Monitor::factory()
            ->count(5)
            ->create()
            ->each(function (Monitor $monitor) {
                MonitorCheck::factory()
                    ->count(20)
                    ->create(['monitor_id' => $monitor->id]);
            });

        $this->command->info('✅ Created 5 monitors with 20 checks each.');
    }
}
