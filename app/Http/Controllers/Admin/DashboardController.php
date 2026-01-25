<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_price');
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', true)->count();
        $outOfStockProducts = Product::where('stock', 0)->count();
        $totalStock = Product::sum('stock');

        // Top Categories
        $totalItemsSold = \App\Models\OrderItem::sum('quantity');
        $topCategories = \App\Models\Category::select('categories.name')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sold')
            ->take(4)
            ->get()
            ->map(function ($category) use ($totalItemsSold) {
                $percentage = $totalItemsSold > 0 ? round(($category->total_sold / $totalItemsSold) * 100) : 0;
                $colors = ['bg-blue-600', 'bg-blue-400', 'bg-purple-400', 'bg-green-400', 'bg-yellow-400', 'bg-red-400'];
                return [
                    'name' => $category->name,
                    'percentage' => $percentage,
                    'color' => $colors[rand(0, 5)], // Random color for now or could be deterministic
                ];
            });
            
        // Assign deterministic colors
        $colors = ['bg-blue-600', 'bg-blue-400', 'bg-purple-400', 'bg-green-400'];
        $topCategories = $topCategories->map(function ($item, $index) use ($colors) {
            $item['color'] = $colors[$index % count($colors)];
            return $item;
        });

        // Recent Orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                $statusColors = [
                    'pending' => 'bg-orange-100 text-orange-700',
                    'processing' => 'bg-blue-100 text-blue-700',
                    'shipped' => 'bg-green-100 text-green-700',
                    'completed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    'paid' => 'bg-purple-100 text-purple-700',
                ];

                return [
                    'id' => '#ORD-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'customer' => $order->user->name,
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($order->user->name) . '&background=random',
                    'amount' => '$' . number_format($order->total_price, 2),
                    'status' => ucfirst($order->status),
                    'statusColor' => $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700',
                    'date' => $order->created_at->format('M d, Y'),
                ];
            });

        // Monthly Revenue (Last 6 months)
        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_price) as value')
            ->where('created_at', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'month' => Carbon::createFromDate($row->year, $row->month, 1)->format('M'),
                    'value' => (float) $row->value,
                    'light' => false, // Can be logic based
                ];
            });
        
        // Ensure we have entries for all last 6 months even if 0
        $filledMonthlyRevenue = collect([]);
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('M');
            $existing = $monthlyRevenue->firstWhere('month', $monthKey);
            $filledMonthlyRevenue->push($existing ?? [
                'month' => $monthKey,
                'value' => 0,
                'light' => false
            ]);
        }

        $ordersByDay = Order::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as order_count, SUM(total_price) as revenue')
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => $row->date,
                    'orders' => (int) $row->order_count,
                    'revenue' => (float) $row->revenue,
                ];
            })
            ->values();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'orders' => $totalOrders,
                'revenue' => $totalRevenue,
                'users' => $totalUsers,
                'products_total' => $totalProducts,
                'products_active' => $activeProducts,
                'products_out_of_stock' => $outOfStockProducts,
                'products_total_stock' => $totalStock,
            ],
            'charts' => [
                'orders_by_day' => $ordersByDay,
                'monthly_revenue' => $filledMonthlyRevenue,
            ],
            'topCategories' => $topCategories,
            'recentOrders' => $recentOrders,
        ]);
    }
}
