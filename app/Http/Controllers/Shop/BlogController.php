<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        // Get categories for filter
        $categories = BlogCategory::where('is_active', true)->pluck('name')->toArray();
        array_unshift($categories, 'All Posts');

        // For the hero section (featured story), we show it only on initial view (no filters/search)
        $featuredStory = null;
        if (!$request->filled('search') && (!$request->filled('category') || $request->category === 'All Posts') && !$request->filled('cursor')) {
            $featuredStory = BlogPost::with('author')
                ->where('status', 'published')
                ->latest('published_at')
                ->first();
        }

        // Build query for stories
        $query = BlogPost::with(['author', 'blogCategory'])
            ->where('status', 'published');

        // Exclude featured story if it exists
        if ($featuredStory) {
            $query->where('id', '!=', $featuredStory->id);
        }

        // Apply Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Apply Category Filter
        if ($request->filled('category') && $request->category !== 'All Posts') {
            $query->whereHas('blogCategory', function($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        // Get latest stories with cursor pagination
        $latestStories = $query->latest('published_at')
            ->cursorPaginate(6)
            ->through(fn($post) => $this->transformPost($post));

        // Trending posts
        $trendingPosts = BlogPost::where('status', 'published')
            ->inRandomOrder()
            ->take(3)
            ->get()
            ->map(function ($post, $index) {
                $post->rank = '0' . ($index + 1);
                return $post;
            });

        $shopTheLook = Product::inRandomOrder()
            ->first();

        if ($shopTheLook) {
             $shopTheLookData = [
                'title' => $shopTheLook->name,
                'collection' => $shopTheLook->category->name ?? 'Collection',
                'price' => (float) $shopTheLook->price,
                'image' => $shopTheLook->image_path,
                'link' => route('shop.show', $shopTheLook->slug),
            ];
        } else {
            $shopTheLookData = null;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'latestStories' => $latestStories,
                'featuredStory' => $featuredStory ? $this->transformPost($featuredStory) : null,
            ]);
        }

        return Inertia::render('Blog/Index', [
            'featuredStory' => $featuredStory ? $this->transformPost($featuredStory) : null,
            'latestStories' => $latestStories,
            'trendingPosts' => $trendingPosts,
            'shopTheLook' => $shopTheLookData,
            'filters' => $categories,
            'currentFilter' => $request->category ?? 'All Posts',
            'searchQuery' => $request->search ?? '',
        ]);
    }

    public function show($slug): Response
    {
        $post = BlogPost::with(['author', 'products', 'comments' => function ($query) {
            $query->withTrashed()
                  ->where('status', 'approved')
                  ->whereNull('parent_id')
                  ->with(['user', 'replies' => function($q) {
                      $q->withTrashed()->where('status', 'approved')->with('user');
                  }])
                  ->latest();
        }])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $shopTheLook = $post->products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name ?? 'Collection',
                'price' => (float) $product->price,
                'image' => $product->image_path,
                'slug' => $product->slug,
            ];
        });

        $transformedPost = $this->transformPost($post);
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user) {
            $transformedPost['author']['is_following'] = $user->isFollowing($post->author);
        } else {
            $transformedPost['author']['is_following'] = false;
        }

        return Inertia::render('Blog/Show', [
            'post' => $transformedPost,
            'comments' => $post->comments,
            'related_posts' => BlogPost::where('category', $post->category)
                ->where('id', '!=', $post->id)
                ->where('status', 'published')
                ->take(3)
                ->get()
                ->map(fn($p) => $this->transformPost($p)),
            'shop_the_look' => $shopTheLook,
        ]);
    }

    private function transformPost($post)
    {
        $wordCount = str_word_count(strip_tags($post->content));
        $readTime = ceil($wordCount / 200);

        return [
            'id' => $post->id,
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'image' => $post->image,
            'category' => $post->blogCategory->name ?? $post->category ?? 'Uncategorized',
            'author' => [
                'id' => $post->author->id,
                'name' => $post->author->name,
                'role' => $post->author->role ?? 'Writer',
                'avatar' => $post->author->avatar,
            ],
            'published_at' => $post->published_at ? $post->published_at->format('M d, Y') : '',
            'read_time' => $readTime . ' min read',
            'description' => $post->excerpt, // Frontend uses description sometimes
            'link' => route('blog.show', $post->slug),
        ];
    }
}
