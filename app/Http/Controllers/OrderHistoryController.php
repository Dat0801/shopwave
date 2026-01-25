<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderHistoryController extends Controller
{
    public function index(Request $request): Response
    {
        $ordersQuery = $request->user()
            ->orders()
            ->with('items.product');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $ordersQuery->where(function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhereHas('items.product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->string('status')->toString());
        }

        if ($request->filled('period')) {
            $period = $request->string('period')->toString();
            if ($period === '30_days') {
                $ordersQuery->whereDate('created_at', '>=', now()->subDays(30));
            } elseif ($period === '3_months') {
                $ordersQuery->whereDate('created_at', '>=', now()->subMonths(3));
            } elseif (is_numeric($period)) {
                $ordersQuery->whereYear('created_at', $period);
            }
        }

        if ($request->filled('from')) {
            $ordersQuery->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $ordersQuery->whereDate('created_at', '<=', $request->date('to'));
        }

        $orders = $ordersQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'period' => $request->string('period')->toString(),
                'from' => $request->string('from')->toString(),
                'to' => $request->string('to')->toString(),
            ],
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        $order->load(['items.product']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update([
            'status' => 'cancelled',
        ]);

        return redirect()->back()->with('success', 'Order cancelled.');
    }
}
