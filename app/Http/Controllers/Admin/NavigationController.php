<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NavigationController extends Controller
{
    public function index(Request $request)
    {
        $location = $request->input('location', 'header');
        
        $items = NavigationItem::where('location', $location)
            ->whereNull('parent_id')
            ->with('children.children') // Support nesting
            ->orderBy('order')
            ->get();
            
        return Inertia::render('Admin/Navigation/Index', [
            'items' => $items,
            'currentLocation' => $location,
            'categories' => Category::select('id', 'name')->get(),
            'pages' => Page::select('id', 'title as name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:custom,category,page,route',
            'url' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'parent_id' => 'nullable|exists:navigation_items,id',
            'location' => 'required|string',
        ]);

        $maxOrder = NavigationItem::where('location', $validated['location'])
            ->where('parent_id', $validated['parent_id'] ?? null)
            ->max('order');

        $validated['order'] = ($maxOrder ?? 0) + 1;

        NavigationItem::create($validated);

        return redirect()->back()->with('success', 'Menu item added successfully.');
    }

    public function update(Request $request, NavigationItem $navigation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:custom,category,page,route',
            'url' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $navigation->update($validated);

        return redirect()->back()->with('success', 'Menu item updated successfully.');
    }

    public function destroy(NavigationItem $navigation)
    {
        $navigation->delete();
        return redirect()->back()->with('success', 'Menu item deleted successfully.');
    }

    public function reorder(Request $request)
    {
        $items = $request->input('items');
        
        $this->updateTree($items, null);

        return redirect()->back()->with('success', 'Menu order updated.');
    }

    private function updateTree($items, $parentId)
    {
        foreach ($items as $index => $item) {
            $navItem = NavigationItem::find($item['id']);
            if ($navItem) {
                $navItem->update([
                    'order' => $index,
                    'parent_id' => $parentId
                ]);

                if (isset($item['children']) && !empty($item['children'])) {
                    $this->updateTree($item['children'], $navItem->id);
                }
            }
        }
    }
}
