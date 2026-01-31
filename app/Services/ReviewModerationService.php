<?php

namespace App\Services;

use App\Models\Review;

class ReviewModerationService
{
    /**
     * List of banned keywords for review moderation.
     */
    private const BANNED_KEYWORDS = [
        'spam',
        'fake',
        'scam',
        'viagra',
        'casino',
        'lottery',
    ];

    /**
     * Determine if a review should be auto-approved based on moderation rules.
     */
    public function shouldAutoApprove(Review $review): bool
    {
        // Only auto-approve if rating is high
        if ($review->rating < 4) {
            return false;
        }

        // Check if user is a verified buyer
        if (! $this->isVerifiedBuyer($review)) {
            return false;
        }

        // Check for spam keywords
        if ($this->containsBannedKeywords($review->comment)) {
            return false;
        }

        return true;
    }

    /**
     * Check if the reviewer is a verified buyer (has purchased the product).
     */
    public function isVerifiedBuyer(Review $review): bool
    {
        return $review->user
            ->orders()
            ->whereHas('items', function ($query) use ($review) {
                $query->where('product_id', $review->product_id);
            })
            ->exists();
    }

    /**
     * Check if comment contains banned keywords.
     */
    public function containsBannedKeywords(string $comment): bool
    {
        $lowerComment = strtolower($comment);

        foreach (self::BANNED_KEYWORDS as $keyword) {
            if (str_contains($lowerComment, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
