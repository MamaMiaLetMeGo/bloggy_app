<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class SecurityAlert extends Notification
{
    use Queueable;

    public function __construct(
        private string $title,
        private string $message,
        private string $actionText = '',
        private string $actionUrl = '',
        private string $level = 'info'
    ) {
        Log::info('🔔 Security Alert Created', [
            'title' => $this->title,
            'message' => $this->message,
            'level' => $this->level,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->subject("Security Alert: {$this->title}")
            ->greeting("Hello {$notifiable->name}!")
            ->line($this->message)
            ->level($this->level);

        if ($this->actionText && $this->actionUrl) {
            $mailMessage->action($this->actionText, $this->actionUrl);
        }

        $mailMessage->line('If you did not initiate this action, please secure your account immediately.')
            ->line('Thank you for helping keep your account secure.')
            ->salutation("Best regards,\n" . config('app.name'));

        // Log the complete email structure
        Log::info('📧 Security Alert Email Details', [
            'to' => $notifiable->email,
            'from' => config('mail.from.address'),
            'subject' => "Security Alert: {$this->title}",
            'content' => [
                'greeting' => "Hello {$notifiable->name}!",
                'main_message' => $this->message,
                'action' => [
                    'text' => $this->actionText,
                    'url' => $this->actionUrl,
                ],
                'warning' => 'If you did not initiate this action, please secure your account immediately.',
                'level' => $this->level,
            ],
            'timestamp' => now()->toDateTimeString()
        ]);

        return $mailMessage;
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'action_text' => $this->actionText,
            'action_url' => $this->actionUrl,
            'level' => $this->level,
        ];
    }
}
