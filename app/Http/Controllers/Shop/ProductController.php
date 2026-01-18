<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::with('category')
            ->where('status', true)
            ->when($request->string('category'), function ($query, $slug) {
                $query->whereHas('category', function ($categoryQuery) use ($slug) {
                    $categoryQuery->where('slug', $slug);
                });
            })
            ->when($request->string('search'), function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get(['id', 'name', 'slug']);

        return Inertia::render('Shop/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'search' => $request->string('search'),
                'category' => $request->string('category'),
            ],
        ]);
    }

    public function show(Product $product): Response
    {
        $product->load('category');

        return Inertia::render('Shop/Show', [
            'product' => $product,
        ]);
    }
}

