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

        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->string('status')->toString());
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
                'status' => $request->string('status')->toString(),
                'from' => $request->string('from')->toString(),
                'to' => $request->string('to')->toString(),
            ],
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
