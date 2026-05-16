<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SiteUpNotification extends Notification
{
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
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your monitored site has **RECOVERED** as of " . now()->toDateTimeString())
            ->line("**URL:** {$this->monitor->url}")
            ->line('Uptime monitoring continues.');
    }
}