<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostNotification extends Notification
{
    use Queueable;

    public function __construct(public Post $post)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New {$this->post->categories->first()->name} Post: {$this->post->title}")
            ->line("A new post has been published in {$this->post->categories->first()->name}!")
            ->line($this->post->excerpt)
            ->action('Read More', route('posts.show', [
                'category' => $this->post->categories->first()->slug,
                'post' => $this->post->slug
            ]));
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
