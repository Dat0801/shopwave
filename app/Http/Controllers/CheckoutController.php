<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Models\Coupon;
use App\Services\OrderService;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly StripePaymentService $stripePaymentService
    ) {}

    public function index(Request $request): Response
    {
        $cart = $request->session()->get('cart', []);

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $couponCode = $request->session()->get('coupon_code');
        $discount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid()) {
                if (! $coupon->min_order_amount || $total >= $coupon->min_order_amount) {
                    if ($coupon->type === 'fixed') {
                        $discount = $coupon->value;
                    } else {
                        $discount = $total * ($coupon->value / 100);
                    }
                }
            }
        }
        $discount = min($discount, $total);

        $user = $request->user();
        $defaultAddress = $user ? $user->addresses()->where('is_default', true)->first() : null;

        // Get user's addresses and payment methods
        $addresses = $user ? $user->addresses()->get() : [];
        $paymentMethods = $user ? $user->paymentMethods()->latest()->get() : [];

        return Inertia::render('Shop/Checkout', [
            'items' => array_values($cart),
            'total' => $total,
            'discount' => $discount,
            'totalAfterDiscount' => $total - $discount,
            'stripePublicKey' => config('stripe.public_key'),
            'user' => $user ? [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ] : null,
            'defaultAddress' => $defaultAddress,
            'addresses' => $addresses->map(fn ($addr) => [
                'id' => $addr->id,
                'label' => $addr->label,
                'name' => $addr->name,
                'phone' => $addr->phone,
                'address_line_1' => $addr->address_line_1,
                'address_line_2' => $addr->address_line_2,
                'city' => $addr->city,
                'state' => $addr->state,
                'postal_code' => $addr->postal_code,
                'country_code' => $addr->country_code,
                'is_default' => $addr->is_default,
            ]),
            'paymentMethods' => $paymentMethods->map(fn ($pm) => [
                'id' => $pm->id,
                'stripe_payment_method_id' => $pm->stripe_payment_method_id,
                'brand' => $pm->stripe_card_brand ?? $pm->type,
                'last4' => $pm->stripe_card_last4 ?? $pm->last4,
                'exp_month' => $pm->stripe_card_exp_month,
                'exp_year' => $pm->stripe_card_exp_year,
                'label' => $pm->label,
                'is_default' => $pm->is_default,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postalCode' => 'required|string|max:20',
            'paymentMethod' => 'required|string|in:credit_card,paypal,cod',
            'paymentMethodId' => 'nullable|integer|exists:payment_methods,id',
        ]);

        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty.');
        }

        $shippingAddress = [
            'first_name' => $request->input('firstName'),
            'last_name' => $request->input('lastName'),
            'address' => $request->input('address'),
            'city' => $request->input('city'),
            'postal_code' => $request->input('postalCode'),
        ];

        $couponCode = $request->session()->get('coupon_code');
        $paymentMethod = $request->input('paymentMethod');
        $paymentMethodId = $request->input('paymentMethodId');

        try {
            $order = $this->orderService->createFromCart($request->user(), $cart, $shippingAddress, $couponCode, $paymentMethod);

            // Send order confirmation email
            Mail::to($request->user()->email)->send(new OrderConfirmation($order));

            // For card payments, redirect to payment page
            if ($paymentMethod === 'credit_card') {
                // Calculate amount in cents
                $totalAmount = $order->total_price - $order->discount_amount;
                $amountInCents = (int) ($totalAmount * 100);

                // If using saved payment method, pass it to payment service
                if ($paymentMethodId) {
                    $payment = $this->stripePaymentService->createPaymentIntentWithSavedCard($order, $amountInCents, $paymentMethodId);
                } else {
                    $payment = $this->stripePaymentService->createPaymentIntent($order, $amountInCents);
                }

                $request->session()->forget('cart');
                $request->session()->forget('coupon_code');

                return redirect()->route('payment.process', ['orderId' => $order->id, 'paymentId' => $payment->id]);
            }

            // For COD or PayPal, order is placed immediately
            $request->session()->forget('cart');
            $request->session()->forget('coupon_code');

            return redirect()->route('orders.index')->with('success', 'Order placed. #'.$order->id);
        } catch (\RuntimeException $exception) {
            return redirect()->route('cart.index')->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('cart.index')->with(
                'error',
                'Unable to place order right now. Please try again.'
            );
        }
    }
}
