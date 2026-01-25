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
            ->when($request->filled('category'), function ($query) use ($request) {
                $slug = $request->string('category')->toString();

                $category = Category::where('slug', $slug)->first();
                if ($category) {
                    $ids = $category->children()->pluck('id')->push($category->id);
                    $query->whereIn('category_id', $ids);
                }
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($request->filled('max_price'), function ($query) use ($request) {
                $query->where('price', '<=', $request->input('max_price'));
            })
            ->when($request->filled('size'), function ($query) use ($request) {
                $sizes = (array) $request->input('size');
                $query->whereHas('variants', function ($q) use ($sizes) {
                    $q->where(function ($subQ) use ($sizes) {
                        foreach ($sizes as $size) {
                            $subQ->orWhereJsonContains('options->Size', $size);
                        }
                    });
                });
            })
            ->when($request->filled('color'), function ($query) use ($request) {
                $colors = (array) $request->input('color');
                $query->whereHas('variants', function ($q) use ($colors) {
                    $q->where(function ($subQ) use ($colors) {
                        foreach ($colors as $color) {
                            $subQ->orWhereJsonContains('options->Color', $color);
                        }
                    });
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::whereNull('parent_id')
            ->where('status', true)
            ->with(['children' => function ($query) {
                $query->where('status', true)
                    ->select('id', 'name', 'slug', 'parent_id')
                    ->orderBy('name');
            }])
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return Inertia::render('Shop/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'search' => $request->string('search')->toString(),
                'category' => $request->string('category')->toString(),
                'sort' => $request->input('sort', 'newest'),
                'min_price' => $request->input('min_price'),
                'max_price' => $request->input('max_price'),
                'size' => $request->input('size'),
                'color' => $request->input('color'),
            ],
        ]);
    }

    public function show(Product $product): Response
    {
        $product->load(['category', 'variants', 'reviews' => function ($query) {
            $query->where('is_approved', true)->with('user')->latest();
        }]);

        return Inertia::render('Shop/Show', [
            'product' => $product,
            'relatedProducts' => Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get(),
        ]);
    }
}
