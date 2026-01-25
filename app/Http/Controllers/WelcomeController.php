<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        $trendingProducts = Product::with('category')->inRandomOrder()->take(4)->get();
        $banners = Banner::where('is_active', true)->orderBy('order')->get();
        $categories = Category::where('status', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->take(8)
            ->get();

        return Inertia::render('Welcome', [
            'trendingProducts' => $trendingProducts,
            'banners' => $banners,
            'categories' => $categories
        ]);
    }
}
