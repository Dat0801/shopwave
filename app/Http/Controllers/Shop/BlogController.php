<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(): Response
    {
        // For the hero section (featured story), we could pick the latest featured one or just the latest one.
        // For now, let's say the latest one is featured.
        $featuredStory = BlogPost::with('author')
            ->where('status', 'published')
            ->latest('published_at')
            ->first();

        // Get latest stories excluding the featured one
        $latestStories = BlogPost::with('author')
            ->where('status', 'published')
            ->where('id', '!=', $featuredStory?->id)
            ->latest('published_at')
            ->take(6)
            ->get();

        // Trending posts - for now just random or most viewed (if we tracked views).
        // Let's just take some random published posts.
        $trendingPosts = BlogPost::where('status', 'published')
            ->inRandomOrder()
            ->take(3)
            ->get()
            ->map(function ($post, $index) {
                $post->rank = '0' . ($index + 1);
                return $post;
            });

        return Inertia::render('Blog/Index', [
            'featuredStory' => $featuredStory ? $this->transformPost($featuredStory) : null,
            'latestStories' => $latestStories->map(fn($post) => $this->transformPost($post)),
            'trendingPosts' => $trendingPosts,
        ]);
    }

    public function show($slug): Response
    {
        $post = BlogPost::with('author')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $shopTheLook = Product::inRandomOrder()
            ->take(3)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Collection',
                    'price' => (float) $product->price,
                    'image' => $product->image_path,
                    'slug' => $product->slug,
                ];
            });

        return Inertia::render('Blog/Show', [
            'post' => $this->transformPost($post),
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
        return [
            'id' => $post->id,
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'image' => $post->image,
            'category' => $post->category,
            'author' => [
                'name' => $post->author->name,
                'role' => $post->author->role ?? 'Writer',
                'avatar' => $post->author->avatar,
            ],
            'published_at' => $post->published_at ? $post->published_at->format('M d, Y') : '',
            'read_time' => '5 min read', // Placeholder or calculate based on content length
            'description' => $post->excerpt, // Frontend uses description sometimes
            'link' => route('blog.show', $post->slug),
        ];
    }
}
