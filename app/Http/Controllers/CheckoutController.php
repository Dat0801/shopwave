<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

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

        return Inertia::render('Shop/Checkout', [
            'items' => array_values($cart),
            'total' => $total,
            'discount' => $discount,
            'totalAfterDiscount' => $total - $discount,
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

        try {
            $order = $this->orderService->createFromCart($request->user(), $cart, $shippingAddress, $couponCode, $paymentMethod);
        } catch (\RuntimeException $exception) {
            return redirect()->route('cart.index')->with('error', $exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('cart.index')->with(
                'error',
                'Unable to place order right now. Please try again.'
            );
        }

        $request->session()->forget('cart');
        $request->session()->forget('coupon_code');

        return redirect()->route('orders.index')->with('success', 'Order placed. #'.$order->id);
    }
}
