<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\NavigationItem;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cart = $request->session()->get('cart', []);

        $cartCount = 0;

        foreach ($cart as $item) {
            $cartCount += (int) ($item['quantity'] ?? 0);
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'navigation' => [
                'header' => NavigationItem::where('location', 'header')
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->with(['children' => function($query) {
                        $query->where('is_active', true)->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get(),
                'footer' => NavigationItem::where('location', 'footer')
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->with(['children' => function($query) {
                        $query->where('is_active', true)->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get(),
                'mobile' => NavigationItem::where('location', 'mobile')
                    ->where('is_active', true)
                    ->whereNull('parent_id')
                    ->with(['children' => function($query) {
                        $query->where('is_active', true)->orderBy('order');
                    }])
                    ->orderBy('order')
                    ->get(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'cart' => [
                'count' => $cartCount,
            ],
        ];
    }
}
