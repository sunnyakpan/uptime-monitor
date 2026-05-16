<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Jobs\CheckMonitorJob;
use App\Models\Monitor;

#[Signature('app:dispatch-monitor-checks')]
#[Description('Command description')]
class DispatchMonitorChecks extends Command
{
    protected $signature   = 'monitors:check';
    protected $description = 'Dispatch check jobs for all due monitors';

    public function handle(): void
    {
        $now = now();

        Monitor::all()->each(function (Monitor $monitor) use ($now) {
            // Check if this monitor is due based on its interval
            $isDue = is_null($monitor->last_checked_at)
                || $monitor->last_checked_at->addMinutes($monitor->check_interval)->lte($now);

            if ($isDue) {
                CheckMonitorJob::dispatch($monitor);
                $this->info("Dispatched check for: {$monitor->url}");
            }
        });
    }
}
