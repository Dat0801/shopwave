<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->string('search').'%');
            $categories = $query->with('parent')->withCount('products')->paginate(10)->withQueryString();
        } else {
            $query->whereNull('parent_id')
                ->withCount('products')
                ->with(['children' => function ($q) {
                    $q->withCount('products')
                      ->with(['children' => function ($q2) {
                          $q2->withCount('products');
                      }]);
                }]);
            $categories = $query->latest()->paginate(10)->withQueryString();
        }

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'allCategories' => Category::orderBy('name')->get(['id', 'name', 'parent_id']),
            'filters' => [
                'search' => $request->string('search'),
            ],
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->storeOnCloudinary('shopwave/categories')->getSecurePath();
        }
        unset($data['image']);

        Category::create($data);

        return redirect()->back()->with('success', 'Category created.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->storeOnCloudinary('shopwave/categories')->getSecurePath();
        }
        unset($data['image']);

        $category->update($data);

        return redirect()->back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted.');
    }
}
