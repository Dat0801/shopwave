<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
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
            ->subject('New Order #' . $this->order->id)
            ->line('A new order has been placed.')
            ->line('Order ID: #' . $this->order->id)
            ->line('Customer: ' . $this->order->user->name)
            ->line('Total: $' . number_format($this->order->total_price, 2))
            ->action('View Order', route('admin.orders.show', $this->order->id))
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
            'type' => 'new_order',
            'order_id' => $this->order->id,
            'customer_name' => $this->order->user->name,
            'total_price' => $this->order->total_price,
            'order_number' => '#' . $this->order->id,
        ];
    }
}
