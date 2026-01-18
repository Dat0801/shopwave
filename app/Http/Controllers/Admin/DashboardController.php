<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_price');
        $totalUsers = User::count();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'orders' => $totalOrders,
                'revenue' => $totalRevenue,
                'users' => $totalUsers,
            ],
        ]);
    }
}

