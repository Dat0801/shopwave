<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::with(['user', 'items.product'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status')->toString());
            })
            ->when($request->filled('from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date('from'));
            })
            ->when($request->filled('to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date('to'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'from' => $request->string('from')->toString(),
                'to' => $request->string('to')->toString(),
            ],
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['user', 'items.product']);

        // Customer Stats
        $customerStats = [
            'total_orders' => $order->user ? $order->user->orders()->count() : 0,
            'total_spent' => $order->user ? $order->user->orders()->whereIn('status', ['paid', 'completed', 'shipped'])->sum('total_price') : 0,
            'customer_since' => $order->user ? $order->user->created_at : null,
        ];

        // Mock Timeline (In a real app, this would come from an OrderHistory model)
        $timeline = [
            [
                'id' => 1,
                'title' => 'Order Placed',
                'description' => "Order #SW-{$order->id} submitted by " . ($order->user ? $order->user->name : 'Guest'),
                'date' => $order->created_at,
                'icon' => 'cart',
                'color' => 'blue',
            ],
        ];

        if ($order->status !== 'pending') {
             // Add more timeline events based on status if needed, 
             // but for now we just show "Order Placed" and maybe "Payment Confirmed" if paid.
             if (in_array($order->status, ['processing', 'paid', 'shipped', 'completed'])) {
                $timeline[] = [
                    'id' => 2,
                    'title' => 'Payment Confirmed',
                    'description' => 'Transaction verified via payment gateway.',
                    'date' => $order->updated_at, // Approximation
                    'icon' => 'credit-card',
                    'color' => 'green',
                ];
             }
             
             if (in_array($order->status, ['processing', 'shipped', 'completed'])) {
                $timeline[] = [
                    'id' => 3,
                    'title' => 'Processing for Shipping',
                    'description' => 'Order marked as processing by Admin.',
                    'date' => $order->updated_at, // Approximation
                    'icon' => 'package',
                    'color' => 'blue',
                ];
             }
        }
        
        // Reverse to show newest first
        $timeline = array_reverse($timeline);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'customerStats' => $customerStats,
            'timeline' => $timeline,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => [
                'nullable',
                'string',
                'max:255',
                'in:pending,processing,paid,shipped,completed,cancelled',
            ],
            'notes' => ['nullable', 'string'],
        ]);

        $order->update(array_filter($validated, fn($value) => !is_null($value)));

        return redirect()->back()->with('success', 'Order updated.');
    }
}
