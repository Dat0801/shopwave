<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WishlistController extends Controller
{
    public function index(Request $request): Response
    {
        $query = $request->user()->wishlist()
            ->with(['product.category'])
            ->whereHas('product', function ($query) {
                $query->whereNull('deleted_at'); // Ensure product is not soft deleted
            });

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $category = $request->input('category');
            if ($category !== 'All Items') {
                $query->whereHas('product.category', function ($q) use ($category) {
                    $q->where('name', $category);
                });
            }
        }

        $wishlistItems = $query->latest()->paginate(12)->withQueryString();

        return Inertia::render('Profile/Wishlist', [
            'wishlistItems' => $wishlistItems,
            'filters' => [
                'search' => $request->input('search'),
                'category' => $request->input('category'),
            ],
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        if (! $user->wishlist()->where('product_id', $product->id)->exists()) {
            $user->wishlist()->create([
                'product_id' => $product->id,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to wishlist.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlist()->where('product_id', $product->id)->delete();

        return redirect()->back()->with('success', 'Product removed from wishlist.');
    }

    public function moveAllToCart(Request $request): RedirectResponse
    {
        $user = $request->user();
        $wishlistItems = $user->wishlist()->with('product')->get();
        $cart = $request->session()->get('cart', []);

        foreach ($wishlistItems as $item) {
            $product = $item->product;
            
            if (!$product || $product->stock <= 0) {
                continue;
            }

            // Logic similar to CartController@store
            if (isset($cart[$product->id])) {
                 // Check stock limit
                 if ($cart[$product->id]['quantity'] < $product->stock) {
                     $cart[$product->id]['quantity'] += 1;
                 }
            } else {
                $price = $product->sale_price ?? $product->price;
                $cart[$product->id] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $price,
                    'quantity' => 1,
                    'image_path' => $product->image_path,
                ];
            }
            
            // Optional: Remove from wishlist after moving to cart?
            // Usually "Move to Cart" implies removal, but "Add to Cart" keeps it.
            // The button says "Move All to Cart", so I will remove them.
            $item->delete();
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', 'All items moved to cart.');
    }
}
