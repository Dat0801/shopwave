<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function index(): Response
    {
        $pages = Page::latest()->paginate(10);
        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages
        ]);
    }

    public function edit(Page $page): Response
    {
        if ($page->slug === 'about-us') {
            return Inertia::render('Admin/Pages/EditAbout', [
                'page' => $page,
            ]);
        }

        if ($page->slug === 'contact' || $page->slug === 'contact-us') {
            return Inertia::render('Admin/Pages/EditContact', [
                'page' => $page,
            ]);
        }

        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'meta' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Process File Uploads in Meta (specifically for Team images)
        $meta = $request->all()['meta'] ?? [];
        
        if (isset($meta['team']) && is_array($meta['team'])) {
            foreach ($meta['team'] as $index => &$member) {
                // Check for file upload
                if ($request->hasFile("meta.team.{$index}.image_file")) {
                    $path = $request->file("meta.team.{$index}.image_file")->store('team', 'public');
                    $member['image'] = Storage::url($path);
                }
                // Remove the file object key to avoid saving it to JSON
                unset($member['image_file']);
            }
        }
        
        $validated['meta'] = $meta;

        $page->update($validated);

        return back()->with('success', 'Page updated successfully.');
    }
}
