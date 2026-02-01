<x-mail::message>
# Your Order Has Shipped!

Good news! Your order #{{ $order->id }} has been shipped and is on its way to you.

## Shipping Details
**Order Number:** #{{ $order->id }}  
**Ship Date:** {{ now()->format('F j, Y') }}  
@if($trackingNumber)
**Tracking Number:** {{ $trackingNumber }}  
@endif
@if($carrier)
**Carrier:** {{ $carrier }}  
@endif

## Shipping Address
{{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}  
{{ $order->shipping_address['address'] ?? '' }}  
{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['postal_code'] ?? '' }}

@if($trackingNumber && $carrier)
You can track your shipment using the tracking number above on the {{ $carrier }} website.
@else
You'll receive tracking information once your package is in transit.
@endif

<x-mail::button :url="route('orders.show', $order->id)">
Track Your Order
</x-mail::button>

Thank you for your purchase!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
