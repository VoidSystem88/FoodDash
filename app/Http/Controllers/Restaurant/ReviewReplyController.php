<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use Illuminate\Http\Request;

class ReviewReplyController extends Controller
{
    /**
     * Restaurant owner replies to a review.
     */
    public function store(Request $request, Review $review)
    {
        $restaurant = auth()->user()->restaurant;
        abort_unless($restaurant && $review->restaurant_id === $restaurant->id, 403);

        // Only 1 reply per review
        if ($review->replies()->exists()) {
            return back()->with('error', 'Naka-reply ka na sa review na ito.');
        }

        $data = $request->validate([
            'body' => 'required|string|min:5|max:1000',
        ]);

        ReviewReply::create([
            'review_id' => $review->id,
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        return back()->with('success', 'Reply posted!');
    }
}