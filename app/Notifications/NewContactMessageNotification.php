<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $contact;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
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
            ->subject('New Contact Message from ' . $this->contact->name)
            ->line('You have received a new contact message.')
            ->line('From: ' . $this->contact->name)
            ->line('Email: ' . $this->contact->email)
            ->line('Subject: ' . $this->contact->subject)
            ->line($this->contact->message)
            ->action('View Message', route('admin.contacts.show', $this->contact->id))
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_contact',
            'contact_id' => $this->contact->id,
            'sender_name' => $this->contact->name,
            'sender_email' => $this->contact->email,
            'subject' => $this->contact->subject,
        ];
    }
}
