<x-mail::message>
# Payment Successful

Great news! Your payment has been processed successfully.

## Payment Details
**Order Number:** #{{ $order->id }}  
**Payment Date:** {{ $payment->paid_at->format('F j, Y \a\t g:i A') }}  
**Payment Method:** {{ ucfirst($payment->payment_method ?? 'Credit Card') }}  
**Amount Paid:** ${{ $amount }}

## Transaction Information
**Transaction ID:** {{ $payment->stripe_payment_intent_id }}  
**Status:** {{ ucfirst($payment->status) }}

Your order is now being prepared for shipment. You'll receive another email when your order ships with tracking information.

<x-mail::button :url="route('orders.show', $order->id)">
View Order
</x-mail::button>

Thank you for your purchase!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
