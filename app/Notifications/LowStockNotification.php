<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $product;
    public $stockLevel;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, int $stockLevel)
    {
        $this->product = $product;
        $this->stockLevel = $stockLevel;
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
            ->subject('Low Stock Alert: ' . $this->product->name)
            ->line('Product "' . $this->product->name . '" is running low on stock.')
            ->line('Current stock: ' . $this->product->stock . ' units')
            ->line('Threshold: ' . $this->stockLevel . ' units')
            ->action('View Product', route('admin.products.show', $this->product->id))
            ->line('Please restock as soon as possible!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'low_stock',
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'current_stock' => $this->product->stock,
            'threshold' => $this->stockLevel,
        ];
    }
}
