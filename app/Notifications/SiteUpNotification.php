<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SiteUpNotification extends Notification
{
    use Queueable;

    public function __construct(public Monitor $monitor) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->success()
            ->subject("🟢 Site Recovered: {$this->monitor->url}")
            ->greeting('Good News: Site is Back Up!')
            ->line("The following site has **RECOVERED** as of " . now()->toDateTimeString())
            ->line("**URL:** {$this->monitor->url}")
            ->line("Consecutive failures: {$this->monitor->consecutive_failures}")
            ->line('Uptime monitoring continues.');
    }
}
