<?php

namespace App\Notifications;

use App\Models\Monitor;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SiteDownNotification extends Notification
{
    public function __construct(public Monitor $monitor) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject("🔴 Site Down: {$this->monitor->url}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your monitored site is **DOWN** as of " . now()->toDateTimeString())
            ->line("**URL:** {$this->monitor->url}")
            ->line("Consecutive failures: {$this->monitor->consecutive_failures}")
            ->line('We will notify you when it recovers.');
    }
}