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
    public function index(): Response
    {
        $reviews = Review::with(['user', 'product'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Approve or reject the specified resource.
     */
    public function toggleApproval(Review $review): RedirectResponse
    {
        $review->update([
            'is_approved' => !$review->is_approved,
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
