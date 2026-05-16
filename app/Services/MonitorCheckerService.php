<?php

namespace App\Services;

use App\Models\Monitor;
use App\Models\MonitorCheck;
use App\Notifications\SiteDownNotification;
use App\Notifications\SiteUpNotification;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Notification;

class MonitorCheckerService
{
    public function check(Monitor $monitor): void
    {
        $client = new Client([
            'timeout'         => 10,
            'connect_timeout' => 10,
            'allow_redirects' => true,
            'http_errors'     => false, // Don't throw on 4xx/5xx
        ]);

        $statusCode     = 0;
        $responseTimeMs = null;
        $isUp           = false;

        try {
            $start = microtime(true);
            $response = $client->get($monitor->url);
            $responseTimeMs = (int) round((microtime(true) - $start) * 1000);

            $statusCode = $response->getStatusCode();
            // 2xx and 3xx are considered "up"
            $isUp = $statusCode >= 200 && $statusCode < 400;

        } catch (ConnectException $e) {
            // Timeout or connection refused — status_code stays 0
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $statusCode = $e->getResponse()->getStatusCode();
                $isUp = false;
            }
        }

        // Record the check
        MonitorCheck::create([
            'monitor_id'       => $monitor->id,
            'status_code'      => $statusCode,
            'response_time_ms' => $responseTimeMs,
            'is_up'            => $isUp,
            'checked_at'       => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $previousStatus = $monitor->status;

        // Update consecutive failures & status
        if ($isUp) {
            $monitor->consecutive_failures = 0;
            $newStatus = 'up';
        } else {
            $monitor->consecutive_failures += 1;
            // Only mark as DOWN after threshold is reached
            $newStatus = $monitor->consecutive_failures >= $monitor->threshold
                ? 'down'
                : $monitor->status; // keep current status until threshold
        }

        $monitor->status         = $newStatus;
        $monitor->last_checked_at = now();
        $monitor->save();

        // Send notifications on status CHANGE
        $this->handleNotifications($monitor, $previousStatus, $newStatus);
    }

    private function handleNotifications(Monitor $monitor, string $previousStatus, string $newStatus): void
    {
        // Site just went DOWN
        if ($newStatus === 'down' && $previousStatus !== 'down') {
            Notification::route('mail', config('uptime.notification_email'))
                ->notify(new SiteDownNotification($monitor));
        }

        // Site came back UP
        if ($newStatus === 'up' && $previousStatus === 'down') {
            Notification::route('mail', config('uptime.notification_email'))
                ->notify(new SiteUpNotification($monitor));
        }
    }
}