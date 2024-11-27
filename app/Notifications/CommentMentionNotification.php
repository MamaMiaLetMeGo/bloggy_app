<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentMentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $postTitle = $this->comment->post->title;
        $commenterName = $this->comment->author_name;
        $commentUrl = url("/posts/{$this->comment->post->slug}#comment-{$this->comment->id}");

        return (new MailMessage)
            ->subject("You were mentioned in a comment on \"{$postTitle}\"")
            ->greeting("Hello {$notifiable->name}!")
            ->line("{$commenterName} mentioned you in a comment:")
            ->line("\"{$this->comment->content}\"")
            ->action('View Comment', $commentUrl)
            ->line('Thank you for being part of our community!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'commenter_name' => $this->comment->author_name,
            'commenter_id' => $this->comment->user_id,
            'comment_content' => $this->comment->content,
            'post_title' => $this->comment->post->title,
        ];
    }
}
