<?php

namespace App\Console\Commands;

use App\Jobs\CheckMonitorJob;
use App\Models\Monitor;
use Illuminate\Console\Command;

class DispatchMonitorChecks extends Command
{
    protected $signature   = 'monitors:check';
    protected $description = 'Dispatch check jobs for all due monitors';

    public function handle(): void
    {
        $now = now();

        $monitors = Monitor::all();

        if ($monitors->isEmpty()) {
            $this->info('No monitors found.');
            return;
        }

        $monitors->each(function (Monitor $monitor) use ($now) {
            $isDue = is_null($monitor->last_checked_at)
                || $monitor->last_checked_at->addMinutes($monitor->check_interval)->lte($now);

            if ($isDue) {
                CheckMonitorJob::dispatch($monitor);
                $this->info("Dispatched check for: {$monitor->url}");
            } else {
                $this->info("Skipped (not due yet): {$monitor->url}");
            }
        });
    }
}