<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(Request $request): Response
    {
        $products = $this->productService->paginatedWithCategory(
            $request->filled('search') ? $request->string('search')->toString() : null,
            10,
            $request->filled('category_id') ? $request->integer('category_id') : null,
            $request->filled('status') ? $request->string('status')->toString() : null,
            $request->filled('stock') ? $request->string('stock')->toString() : null
        );

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $this->productService->categoriesForSelect(),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'category_id' => $request->string('category_id')->toString(),
                'status' => $request->string('status')->toString(),
                'stock' => $request->string('stock')->toString(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Create', [
            'categories' => $this->productService->categoriesForSelect(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated());

        return redirect()->back()->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return redirect()->back()->with('success', 'Product deleted.');
    }
}
