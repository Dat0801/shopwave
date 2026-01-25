<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    protected function cart(Request $request): array
    {
        return $request->session()->get('cart', []);
    }

    protected function saveCart(Request $request, array $cart): void
    {
        $request->session()->put('cart', $cart);
    }

    public function index(Request $request): Response
    {
        $cart = $this->cart($request);

        $total = 0;

        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return Inertia::render('Shop/Cart', [
            'items' => array_values($cart),
            'total' => $total,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->cart($request);

        $quantity = (int) $request->input('quantity', 1);

        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'This product is out of stock.');
        }

        $currentQuantity = $cart[$product->id]['quantity'] ?? 0;
        $desiredQuantity = $currentQuantity + $quantity;

        if ($desiredQuantity > $product->stock) {
            return redirect()->back()->with(
                'error',
                'Not enough stock for '.$product->name.'. Only '.$product->stock.' left.'
            );
        }

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $price = $product->sale_price ?? $product->price;

            $cart[$product->id] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'image_path' => $product->image_path,
            ];
        }

        $this->saveCart($request, $cart);

        return redirect()->back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->cart($request);

        if (! isset($cart[$product->id])) {
            return redirect()->back()->with('error', 'This item is not in your cart.');
        }

        if ($product->stock <= 0) {
            unset($cart[$product->id]);

            $this->saveCart($request, $cart);

            return redirect()->back()->with('error', 'This product is now out of stock and was removed from your cart.');
        }

        $quantity = (int) $request->input('quantity');

        if ($quantity > $product->stock) {
            return redirect()->back()->with(
                'error',
                'Not enough stock for '.$product->name.'. Only '.$product->stock.' left.'
            );
        }

        $cart[$product->id]['quantity'] = $quantity;

        $this->saveCart($request, $cart);

        return redirect()->back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $cart = $this->cart($request);

        unset($cart[$product->id]);

        $this->saveCart($request, $cart);

        return redirect()->back()->with('success', 'Item removed.');
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);
        $code = $request->input('code');

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            return redirect()->back()->with('error', 'Invalid coupon code.');
        }

        if (! $coupon->isValid()) {
            return redirect()->back()->with('error', 'Coupon is expired or invalid.');
        }

        // Check min order amount
        $cart = $this->cart($request);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if ($coupon->min_order_amount && $total < $coupon->min_order_amount) {
            return redirect()->back()->with('error', 'Minimum order amount for this coupon is '.$coupon->min_order_amount);
        }

        $request->session()->put('coupon_code', $code);

        return redirect()->back()->with('success', 'Coupon applied successfully.');
    }

    public function removeCoupon(Request $request): RedirectResponse
    {
        $request->session()->forget('coupon_code');

        return redirect()->back()->with('success', 'Coupon removed.');
    }
}
