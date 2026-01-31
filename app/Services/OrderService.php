<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Coupon;
use App\Notifications\NewOrderNotification;
use App\Notifications\LowStockNotification;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createFromCart(User $user, array $cart, array $shippingAddress = null, ?string $couponCode = null, string $paymentMethod = 'cod'): Order
    {
        return DB::transaction(function () use ($user, $cart, $shippingAddress, $couponCode, $paymentMethod) {
            $total = 0;

            foreach ($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            $discountAmount = 0;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && $coupon->isValid()) {
                    if (! $coupon->min_order_amount || $total >= $coupon->min_order_amount) {
                        if ($coupon->type === 'fixed') {
                            $discountAmount = $coupon->value;
                        } else {
                            $discountAmount = $total * ($coupon->value / 100);
                        }

                        $discountAmount = min($discountAmount, $total);

                        $coupon->increment('used_count');
                    }
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $total - $discountAmount,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode,
                'status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentMethod === 'cod' ? 'pending' : 'paid',
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \RuntimeException('Not enough stock for '.$product->name);
                }

                $product->decrement('stock', $item['quantity']);

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Check if product stock is low and notify admins
                if ($product->stock <= 10) {
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new LowStockNotification($product, 10));
                    }
                }
            }

            // Send notification to all admins about new order
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new NewOrderNotification($order));
            }

            return $order;
        });
    }
}

