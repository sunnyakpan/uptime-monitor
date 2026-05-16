<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Services\MonitorCheckerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckMonitorJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Monitor $monitor) {}

    public function handle(MonitorCheckerService $checker): void
    {
        $checker->check($this->monitor);
    }
}