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
            ],
        ]);
    }
}
