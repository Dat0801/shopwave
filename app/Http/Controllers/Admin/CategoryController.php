<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            // When not searching, return the full tree for drag-and-drop reordering
            $categories = Category::whereNull('parent_id')
                ->withCount('products')
                ->with(['children' => function ($q) {
                    $q->orderBy('order')
                      ->withCount('products')
                      ->with(['children' => function ($q2) {
                          $q2->orderBy('order')->withCount('products');
                      }]);
                }])
                ->orderBy('order')
                ->get();
        }

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'allCategories' => Category::orderBy('name')->get(['id', 'name', 'parent_id']),
            'filters' => [
                'search' => $request->string('search'),
            ],
        ]);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.children' => 'nullable|array',
        ]);

        $this->updateOrder($request->categories, null);

        return redirect()->back()->with('success', 'Categories reordered successfully.');
    }

    private function updateOrder(array $categories, ?int $parentId)
    {
        foreach ($categories as $index => $categoryData) {
            $category = Category::find($categoryData['id']);
            if ($category) {
                $category->update([
                    'parent_id' => $parentId,
                    'order' => $index + 1,
                ]);

                if (isset($categoryData['children']) && !empty($categoryData['children'])) {
                    $this->updateOrder($categoryData['children'], $category->id);
                }
            }
        }
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
