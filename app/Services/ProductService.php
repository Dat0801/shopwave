<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function paginatedWithCategory(
        ?string $search = null,
        int $perPage = 12,
        ?int $categoryId = null,
        ?string $status = null,
        ?string $stock = null
    ): LengthAwarePaginator
    {
        return Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when(! is_null($status), function ($query) use ($status) {
                $query->where('status', $status === 'active');
            })
            ->when($stock, function ($query) use ($stock) {
                if ($stock === 'out') {
                    $query->where('stock', '=', 0);
                } elseif ($stock === 'low') {
                    $query->whereBetween('stock', [1, 5]);
                } elseif ($stock === 'in') {
                    $query->where('stock', '>', 0);
                }
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Product
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $data['image']->store('products', 'public');
        }

        unset($data['image']);

        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image_path'] = $data['image']->store('products', 'public');
        }

        unset($data['image']);

        $product->update($data);

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function categoriesForSelect()
    {
        return Category::orderBy('name')->get(['id', 'name']);
    }
}
