<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('placement') && $request->placement !== 'All Banners') {
            $query->where('placement', $request->placement);
        }

        $banners = $query->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Banners/Index', [
            'banners' => $banners,
            'filters' => $request->only(['search', 'placement']),
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.order' => 'required|integer',
        ]);

        foreach ($request->banners as $item) {
            Banner::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return back()->with('success', 'Banners reordered successfully.');
    }

    public function create()
    {
        return Inertia::render('Admin/Banners/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'required|image|max:2048',
            'mobile_image' => 'nullable|image|max:2048',
            'link' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'placement' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'integer',
            'duration' => 'integer|min:1000',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->storeOnCloudinary('shopwave/banners')->getSecurePath();
        }

        if ($request->hasFile('mobile_image')) {
            $validated['mobile_image_path'] = $request->file('mobile_image')->storeOnCloudinary('shopwave/banners')->getSecurePath();
        }

        unset($validated['image']);
        unset($validated['mobile_image']);

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function show(Banner $banner)
    {
        return Inertia::render('Admin/Banners/Show', [
            'banner' => $banner
        ]);
    }

    public function edit(Banner $banner)
    {
        return Inertia::render('Admin/Banners/Edit', [
            'banner' => $banner
        ]);
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'mobile_image' => 'nullable|image|max:2048',
            'link' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'placement' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'order' => 'integer',
            'duration' => 'integer|min:1000',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->storeOnCloudinary('shopwave/banners')->getSecurePath();
        }

        if ($request->hasFile('mobile_image')) {
            $validated['mobile_image_path'] = $request->file('mobile_image')->storeOnCloudinary('shopwave/banners')->getSecurePath();
        }

        unset($validated['image']);
        unset($validated['mobile_image']);

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->back()->with('success', 'Banner deleted successfully.');
    }
}
