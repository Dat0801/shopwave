<x-mail::message>
# Order Confirmation

Thank you for your order! Your order has been received and is being processed.

## Order Details
**Order Number:** #{{ $order->id }}  
**Order Date:** {{ $order->created_at->format('F j, Y') }}  
**Payment Method:** {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}

## Items Ordered

<x-mail::table>
| Product | Quantity | Price |
| :------ | :------: | ----: |
@foreach($items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
</x-mail::table>

## Order Summary

**Subtotal:** ${{ number_format($subtotal, 2) }}  
@if($order->discount_amount > 0)
**Discount:** -${{ number_format($order->discount_amount, 2) }}  
@endif
**Shipping:** Free  
**Total:** ${{ number_format($order->total_price, 2) }}

## Shipping Address

{{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}  
{{ $order->shipping_address['address'] ?? '' }}  
{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['postal_code'] ?? '' }}

<x-mail::button :url="route('orders.show', $order->id)">
View Order Details
</x-mail::button>

If you have any questions, please don't hesitate to contact us.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
