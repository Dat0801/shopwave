<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where('code', 'like', "%{$search}%");
        }

        if ($request->filled('status') && $request->status !== 'All') {
            switch ($request->status) {
                case 'Active':
                    $query->where('is_active', true)
                          ->where(function ($q) {
                              $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                          })
                          ->where(function ($q) {
                              $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                          });
                    break;
                case 'Scheduled':
                    $query->where('is_active', true)
                          ->where('starts_at', '>', now());
                    break;
                case 'Expired':
                    $query->where('expires_at', '<=', now());
                    break;
                case 'Draft': // Assuming Draft means Inactive for now based on UI toggle
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('type', strtolower($request->type));
        }

        $coupons = $query->latest()->paginate(10)->withQueryString();

        // Calculate Stats
        $activeCouponsCount = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->count();
        
        $totalRedemptions = Coupon::sum('used_count');
        
        // Approximate revenue saved (only fixed amount coupons can be calculated accurately without order context)
        $revenueSaved = Coupon::where('type', 'fixed')->sum(DB::raw('used_count * value'));

        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons,
            'stats' => [
                'active_coupons' => $activeCouponsCount,
                'total_redemptions' => $totalRedemptions,
                'revenue_saved' => $revenueSaved,
            ],
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'type' => $request->string('type')->toString(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Coupons/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'limit_usage_per_user' => 'boolean',
            'is_active' => 'boolean',
        ]);

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon): Response
    {
        return Inertia::render('Admin/Coupons/Edit', [
            'coupon' => $coupon,
        ]);
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'limit_usage_per_user' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }
}
