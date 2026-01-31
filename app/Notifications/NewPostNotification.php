<?php

namespace App\Notifications;

use App\Models\BlogPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $post;

    /**
     * Create a new notification instance.
     */
    public function __construct(BlogPost $post)
    {
        $this->post = $post;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Post from ' . $this->post->author->name)
            ->line($this->post->author->name . ' published a new post: ' . $this->post->title)
            ->action('Read Post', route('blog.show', $this->post->slug))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'slug' => $this->post->slug,
            'author_id' => $this->post->author_id,
            'author_name' => $this->post->author->name,
            'message' => $this->post->author->name . ' published a new post: ' . $this->post->title,
            'type' => 'new_post',
        ];
    }
}
