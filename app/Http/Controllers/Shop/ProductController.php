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
        // Load relationships with specific attributes for optimization
        $product->load([
            'category:id,name,slug',
            'variants' => function ($query) {
                $query->where('active', true)
                    ->select('id', 'product_id', 'name', 'sku', 'price', 'stock', 'options');
            },
            'reviews' => function ($query) {
                $query->where('status', 'approved')
                    ->with('user:id,name')
                    ->latest()
                    ->select('id', 'user_id', 'product_id', 'rating', 'comment', 'status', 'created_at');
            },
        ]);

        // Get related products with necessary fields
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', true)
            ->select('id', 'name', 'slug', 'price', 'sale_price', 'image_path')
            ->take(4)
            ->get();

        // Calculate review statistics
        $reviewStats = [
            'average' => $product->reviews->avg('rating') ?? 0,
            'total' => $product->reviews->count(),
            'distribution' => [
                5 => $product->reviews->where('rating', 5)->count(),
                4 => $product->reviews->where('rating', 4)->count(),
                3 => $product->reviews->where('rating', 3)->count(),
                2 => $product->reviews->where('rating', 2)->count(),
                1 => $product->reviews->where('rating', 1)->count(),
            ],
        ];

        // Extract available colors and sizes from variants
        $colors = collect();
        $sizes = collect();

        foreach ($product->variants as $variant) {
            if (isset($variant->options['Color'])) {
                $colors->push($variant->options['Color']);
            }
            if (isset($variant->options['Size'])) {
                $sizes->push($variant->options['Size']);
            }
        }

        $availableOptions = [
            'colors' => $colors->unique()->filter()->values(),
            'sizes' => $sizes->unique()->filter()->values(),
        ];

        return Inertia::render('Shop/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviewStats' => $reviewStats,
            'availableOptions' => $availableOptions,
        ]);
    }
}
