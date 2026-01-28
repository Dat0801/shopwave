<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Review::with(['user', 'product']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $reviews = $query->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('status', 'pending')->count(),
            'avg_rating' => round(Review::avg('rating') ?? 0, 1),
        ];

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
            'stats' => $stats,
            'filters' => $request->only(['status']),
        ]);
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, Review $review): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Review status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
