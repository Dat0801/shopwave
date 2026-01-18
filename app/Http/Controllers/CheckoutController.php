<?php

namespace App\Http\Controllers;

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

        return Inertia::render('Shop/Checkout', [
            'items' => array_values($cart),
            'total' => $total,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Cart is empty.');
        }

        try {
            $order = $this->orderService->createFromCart($request->user(), $cart);
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

        return redirect()->route('orders.index')->with('success', 'Order placed. #'.$order->id);
    }
}
