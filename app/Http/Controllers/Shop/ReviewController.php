<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\StoreReviewRequest;
use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewModerationService;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function __construct(private readonly ReviewModerationService $moderationService) {}

    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this product.');
        }

        $validated = $request->validated();

        $review = Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending',
        ]);

        // Auto-approve if meets moderation criteria
        if ($this->moderationService->shouldAutoApprove($review)) {
            $review->update(['status' => 'approved']);

            return redirect()->back()->with('success', 'Thank you! Your review has been published.');
        }

        return redirect()->back()->with('success', 'Review submitted successfully and is pending approval.');
    }
}
