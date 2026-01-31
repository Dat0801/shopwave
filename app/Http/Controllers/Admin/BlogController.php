<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'blogCategory'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($request->input('category'), function ($query, $category) {
                if ($category !== 'All Categories') {
                    // Check if it's an ID or Name. Since existing code sends Name, 
                    // and we want to support both or switch to ID.
                    // If we switch UI to send ID, we should check ID.
                    // But legacy filter uses name 'Style Guide'.
                    // Let's try to match both for robustness.
                    $query->where(function ($q) use ($category) {
                        $q->where('category', $category)
                          ->orWhere('blog_category_id', $category);
                    });
                }
            })
            ->when($request->input('status'), function ($query, $status) {
                if ($status && $status !== 'Status: All') {
                    $query->where('status', strtolower($status));
                }
            });

        $posts = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Blog/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'category', 'status']),
            'categories' => BlogCategory::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Blog/Create', [
            'categories' => BlogCategory::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        // Populate legacy category string
        $category = BlogCategory::find($validated['blog_category_id']);
        $validated['category'] = $category->name;

        $validated['slug'] = Str::slug($validated['title']);
        $validated['author_id'] = Auth::id();

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = BlogPost::create($validated);

        if ($post->status === 'published') {
            /** @var \App\Models\User $author */
            $author = Auth::user();
            Notification::send($author->followers, new NewPostNotification($post));
        }

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created successfully.');
    }

    public function show(BlogPost $blog)
    {
        $blog->load(['author', 'blogCategory']);
        
        return Inertia::render('Admin/Blog/Show', [
            'post' => $blog,
        ]);
    }

    public function edit(BlogPost $blog)
    {
        return Inertia::render('Admin/Blog/Edit', [
            'post' => $blog,
            'categories' => BlogCategory::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog', 'public');
            $validated['image'] = '/storage/' . $path;
        } else {
            unset($validated['image']);
        }

        // Populate legacy category string
        $category = BlogCategory::find($validated['blog_category_id']);
        $validated['category'] = $category->name;

        if ($request->has('title') && $request->title !== $blog->title) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blog)
    {
        $blog->delete();

        return redirect()->route('admin.blog.index')->with('success', 'Blog post deleted successfully.');
    }
}
