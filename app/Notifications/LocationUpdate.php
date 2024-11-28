<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LocationUpdate extends Notification
{
    protected $locationData;

    public function __construct($locationData)
    {
        $this->locationData = $locationData;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Location Update')
            ->line('New location update received:')
            ->line("Latitude: {$this->locationData['latitude']}")
            ->line("Longitude: {$this->locationData['longitude']}")
            ->line("Time: " . \Carbon\Carbon::parse($this->locationData['timestamp'])->format('Y-m-d H:i:s'));
    }
}